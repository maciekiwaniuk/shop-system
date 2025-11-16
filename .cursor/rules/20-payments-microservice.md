# Payments Microservice (Go) Architecture & Rules

The payments microservice is a standalone application written in Go, following Hexagonal Architecture, CQRS, and Domain-Driven Design (DDD) principles. It is responsible for handling all payment-related logic.

## 1. Project Structure (Hexagonal Architecture)

The service is located in `microservices/payments/` and follows a strict hexagonal structure within the `internal/` directory.

- `internal/`
    - `app/` - The core application logic (use cases).
        - `command/` - Command handlers.
        - `query/` - Query handlers.
    - `domain/` - Core business logic, entities, and aggregates (`payer.go`, `transaction.go`).
    - `ports/` - Interfaces defining how the application communicates with the outside world.
        - `http/` - Inbound port for the HTTP API (Gin handlers).
        - `outbound/` - Outbound ports, e.g., for publishing events (`event_publisher.go`).
        - `external/` - Ports for communicating with other services.
    - `adapters/` - Concrete implementations (adapters) for the ports.
        - `db/` - Database logic, including repositories and `sqlc` generated code.
        - `messaging/` - Implementation of the event publisher port (e.g., RabbitMQ).
        - `service/` - Implementation for external service communication.
    - `cmd/` - The main entry point of the application.

### Layer-Specific Rules:

#### `domain` Layer
- Contains the core business logic: Aggregates (`Payer`, `Transaction`), Value Objects, and Domain Events.
- **Rule**: This layer is pure Go. It has **zero dependencies** on external libraries, frameworks, or infrastructure code (no database, no messaging, no HTTP).
- **Rule**: Aggregates must protect their own invariants. State changes are performed through methods on the aggregate struct.

#### `app` Layer
- Orchestrates the domain logic to fulfill use cases.
- Contains all Command and Query handlers.
- **Rule**: This layer acts as the mediator. It retrieves aggregates via repository interfaces (ports), calls their domain methods, and uses the repository to persist changes. It does not contain business logic itself.

#### `ports` Layer
- Defines the contracts (Go interfaces) for communication.
- **Inbound Ports**: Define how the outside world drives the application (e.g., an `HttpHandler` interface).
- **Outbound Ports**: Define the application's requirements from the outside world (e.g., a `PayerRepository` or `EventPublisher` interface).
- **Rule**: This layer contains only interfaces. No concrete implementations.

#### `adapters` Layer
- Provides the concrete implementations for the interfaces defined in the `ports` layer.
- **Rule**: This is the **ONLY** layer where infrastructure-specific code and dependencies are allowed (e.g., `database/sql`, Gin, RabbitMQ client libraries).

## 2. CQRS Implementation

- CQRS is strictly enforced to separate writes (Commands) from reads (Queries).

#### Commands (Write Model)
- Handlers are located in `internal/app/command/`.
- **Rule**: Command handlers receive a command struct, interact with the domain aggregates, and use repositories to persist changes.
- **Rule**: Command handlers should return an `error`. On success, they return `nil`. If an ID or data needs to be returned to the caller (e.g., in an HTTP response), the `ports/http` handler is responsible for retrieving it.

#### Queries (Read Model)
- Handlers are located in `internal/app/query/`.
- **Rule**: Query handlers are for reading data only. They **must not** change the state of the system.
- **Rule**: Query handlers should be highly optimized for reads. They should directly query the database using the `sqlc`-generated queries to build and return read models (DTOs). They should not use the domain aggregates.
- **Rule**: Query handlers return a result struct and an `error`, e.g., `(Transaction, error)`.

## 3. Database & Persistence

- The database schema is managed via SQL migration files in `internal/adapters/db/migrations/`.
- The `sqlc` tool is used to generate type-safe Go code from raw SQL queries.
- SQL queries for `sqlc` are located in `internal/adapters/db/query/`.
- **Rule**: To update database access logic, first modify the `.sql` files in `internal/adapters/db/query/`, then run the `scripts/generate-go-from-sql.sh` script to regenerate the Go code. Do not manually edit the generated files.
- Repositories that implement the domain's persistence ports are located in `internal/adapters/db/repository/`. They use the `sqlc`-generated code internally.

## 4. Coding Style & Conventions

- Follow standard Go conventions (`gofmt`, `go vet`).
- Use dependency injection. Dependencies should be passed into structs at creation time.
- Handle errors explicitly. Errors should be checked and returned up the call stack. Use error wrapping to add context where appropriate.
- Structs and interfaces should be named thoughtfully. Avoid stutter (e.g., `payer.Payer`).
