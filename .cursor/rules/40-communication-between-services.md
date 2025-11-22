# 4. Communication Between Services

This document outlines the communication patterns between the frontend, the backend monolith (PHP), its internal modules, and the payments microservice (Go).

## Communication Matrix

| From     | To                | Protocol / Pattern                                  | Purpose                                               |
|----------|-------------------|-----------------------------------------------------|-------------------------------------------------------|
| Frontend | Backend (PHP)     | HTTP API (RESTful)                                  | Auth, Product Catalog, Order Management               |
| Frontend | Payments (Go)     | HTTP API (RESTful)                                  | Simulating payment completion/cancellation            |
| Backend  | Backend           | Sync Domain Events (Symfony EventDispatcher)        | Decoupling Auth and Commerce modules                  |
| Backend  | Payments (Go)     | Sync HTTP API Call                                  | Initiating a transaction when an order is created     |
| Payments | Backend           | Async Events (RabbitMQ)                             | Notifying the backend of transaction status changes   |

---

## 1. Frontend ↔ Backend & Payments Communication

The frontend acts as the primary initiator for most user-facing actions.

### Frontend → Backend (PHP Monolith)

- **How**: The frontend makes RESTful API calls to the Symfony backend. Client-side API logic is centralized in `frontend/src/lib/api/`.
    - `auth.ts`: Handles user login and registration.
    - `products.ts`: Fetches product information.
    - `orders.ts`: Creates and retrieves user orders.
- **Example Flow (User Registration)**:
    1. User submits the registration form on the frontend.
    2. The frontend calls the registration function in `auth.ts`.
    3. An API request is sent to the `/api/register` endpoint on the Symfony backend (`Auth` module).
    4. The backend processes the registration.

### Frontend → Payments (Go Microservice)

- **How**: The frontend makes direct API calls to the Go payments microservice to simulate payment actions. The API client logic is in `frontend/src/lib/api/payments.ts`.
- **Example Flow (Faking a Payment)**:
    1. A user is on the order page and clicks "Pay for Order".
    2. The frontend calls a function in `payments.ts` that sends a request to the `/transactions/complete` endpoint on the Go microservice.
    3. The Go microservice processes this request and publishes an event to RabbitMQ.

---

## 2. Backend Module-to-Module Communication (PHP)

Communication between modules inside the monolith is handled via a synchronous, in-memory event bus to maintain decoupling while ensuring immediate consistency.

- **How**: One module dispatches a domain event using Symfony's `EventDispatcher`. Other modules have listeners that subscribe to this event. While this is asynchronous from an architectural standpoint (the modules don't know about each other), the execution is synchronous—the listeners run immediately within the same process.
- **Example Flow (User Registration creates a Client)**:
    1. A user registers via the API endpoint in the `Auth` module.
    2. The `CreateUserCommandHandler` in the `Auth` module successfully creates a new user.
    3. It dispatches a `UserRegisteredEvent` using the `EventDispatcherInterface`.
    4. The `CreateClientOnRegistrationListener` in the `Commerce` module is subscribed to this event and runs immediately.
    5. The listener triggers a `CreateClientCommand` within the `Commerce` module, creating a corresponding `Client` entity. This ensures that every registered user also exists as a client before the initial request is completed.

---

## 3. Backend ↔ Payments Microservice Communication

This interaction is a mix of synchronous and asynchronous patterns.

### Backend (PHP) → Payments (Go) - *Synchronous*

- **How**: When an order is created in the backend, it makes a direct, synchronous HTTP call to the payments microservice to create a corresponding transaction record. This is essential to ensure an order is not created without a valid transaction placeholder.
- **Example Flow (Order Creation)**:
    1. The `CreateOrderCommandHandler` in the `Commerce` module successfully creates an `Order` entity.
    2. It then calls the `init()` method on the `PaymentsInitializerInterface`.
    3. The implementation of this interface (the adapter) makes an HTTP POST request to the `/transactions` endpoint of the Go microservice, passing the order ID, user ID, and total cost.
    4. The payments microservice creates a new transaction with a "pending" status and returns a success/failure response. The backend requires this to happen successfully before completing the order creation process.

### Payments (Go) → Backend (PHP) - *Asynchronous*

- **How**: After a transaction's state is changed (e.g., paid or canceled), the Go microservice publishes an event to a RabbitMQ exchange. The PHP backend has consumer workers that listen for these events.
- **Example Flow (Payment Completion)**:
    1. The user "pays" for the order on the frontend, which calls the `/transactions/complete` endpoint on the Go microservice.
    2. The `CompleteTransactionHandler` in the Go service updates the transaction status in its database to "paid".
    3. It then uses an `EventPublisher` to publish a `TransactionCompletedEvent` (containing the transaction ID) to RabbitMQ.
    4. The payments consumer worker in the PHP backend receives this message from the `payments_events` queue.
    5. The `TransactionCompletedEventHandler` (marked with `#[AsMessageHandler]`) processes the event and dispatches a `ChangeOrderStatusCommand` within the `Commerce` module to update the corresponding order's status to `COMPLETED`.

---

## 4. Message Queue & Consumer Workers

The system uses RabbitMQ with Symfony Messenger for asynchronous message processing. There are two types of workers running as separate Docker containers/Kubernetes pods.

### Worker Types

#### Async Worker (`shop-system-worker-async`)
- **Consumes from**: `async` transport (queue: `messages`)
- **Processes**: Symfony async commands (e.g., `SendWelcomeEmailCommand`)
- **Serialization**: PHP native serialization (Symfony default)
- **Use case**: Background tasks like sending emails, generating reports

#### Payments Worker (`shop-system-worker-payments`)
- **Consumes from**: `payments_events` transport (queue: `payments_events_queue`)
- **Processes**: Payment events from Go microservice
- **Serialization**: `PaymentsEventSerializer` (custom JSON deserializer)
- **Routing Keys**: `transaction_completed`, `transaction_canceled`
- **Use case**: Reacting to payment status changes

### Message Flow

```
Symfony Async Command → async transport → RabbitMQ → Async Worker → Handler
Go Payment Event → payments_events exchange → RabbitMQ → Payments Worker → Handler
```

### Configuration

Located in `config/packages/messenger.yaml`:

- **Transports**: `async`, `payments_events`, `failed` (DLQ)
- **Retry Strategy**: 3 retries with exponential backoff (1s → 2s → 4s)
- **Failed Messages**: Automatically routed to `failed` transport after max retries
- **Middleware**: `ErrorLoggingMiddleware` logs all errors with detailed context

### Error Handling

- **ErrorLoggingMiddleware** (`src/Common/Infrastructure/Messenger/ErrorLoggingMiddleware.php`):
    - Intercepts all exceptions in message handlers
    - Logs exception details, message data, and stack trace
    - Re-throws to allow retry mechanism
    - Only processes messages from transport (not locally dispatched)

- **Dead Letter Queue (DLQ)**:
    - Failed messages (after 3 retries) go to `failed` transport
    - View: `php bin/console app:messenger:failed-messages`
    - Retry: `php bin/console messenger:failed:retry --force`

### Running Workers

**Docker Compose** (local development):
```bash
# Workers start automatically with docker-compose up
docker logs -f shop-system-worker-async
docker logs -f shop-system-worker-payments
```

**Kubernetes**:
```bash
kubectl apply -f k8s/local/backend/queue.yaml
kubectl logs -f deployment/queue -n shop-system
```

**Manual** (for testing):
```bash
php bin/console messenger:consume async -vv
php bin/console messenger:consume payments_events -vv
```

### Message Formats

**Symfony Async Message** (PHP serialized):
```
O:36:"Symfony\\Component\\Messenger\\Envelope":2:{...}
```

**Payment Event** (JSON from Go):
```json
{
    "transaction_id": "12b0d3e0-befa-11f0-aa62-2f547971bbe0",
    "canceled_at": "2025-11-11T12:33:31Z",
    "routing_key": "transaction_canceled"
}
```

### Best Practices

- **Rule**: Message handlers must be idempotent (safe to retry)
- **Rule**: Always log errors with context in handlers
- **Rule**: Use `#[AsMessageHandler]` attribute on handler classes
- **Rule**: Async command handlers must have `void` return type
- **Rule**: Monitor the `failed` transport regularly for problematic messages
