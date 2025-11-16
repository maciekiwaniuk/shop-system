# Backend Architecture & Rules

The backend is a modular monolith built with PHP and Symfony, following Hexagonal Architecture, CQRS, and Domain-Driven Design (DDD) principles. Adherence to these patterns is strict to ensure code quality, maintainability, and scalability.

## 1. Modular Monolith

- The application is divided into business-capability-aligned modules located in `src/Module/`.
- Current modules are `Auth` and `Commerce`.
- **Rule**: Modules are isolated bounded contexts. They must not depend directly on each other's internal code (e.g., one module's service cannot call another module's service).
- **Rule**: Communication between modules should happen asynchronously via the message bus (for commands/events) or by querying another module's public read API (query handlers). Direct service calls between modules are forbidden.

## 2. Module Structure (Hexagonal Architecture)

Each module follows a strict 4-layer hexagonal structure. When adding new classes, they must be placed in the correct layer.

- `src/Module/{ModuleName}/`
    - `Application/` - Use Cases, Command/Query Handlers, DTOs
    - `Domain/` - Core business logic, Entities, Aggregates, Repository Interfaces (Ports)
    - `Infrastructure/` - Implementation of ports (Adapters), e.g., Doctrine Repos, external service clients
    - `Interface/` - Entry points: Controllers, CLI Commands, Message Consumers

### Layer-Specific Rules:

#### `Domain` Layer
- Contains the core, framework-agnostic business logic.
- Consists of Aggregates, Entities, Value Objects, Domain Events, and Repository Interfaces (Ports).
- **Rule**: **Zero external dependencies.** This layer must not depend on Symfony, Doctrine, or any other framework. It is pure PHP and business logic.
- **Rule**: Aggregates are the consistency boundary. All state changes must be initiated through aggregate methods that enforce business invariants.

#### `Application` Layer
- Orchestrates the domain logic to fulfill application-specific use cases.
- Contains Command Handlers, Query Handlers, and application-level DTOs (Commands, Queries, Results).
- **Rule**: This layer acts as a mediator. It fetches aggregates from repositories, calls their methods, and uses the repository to persist them back.
- **Rule**: Application services should be thin. All complex business rules belong in the `Domain` layer.

#### `Infrastructure` Layer
- Contains the implementation details and adapters for interfaces defined in the `Domain` or `Application` layers.
- Examples: `Doctrine*Repository.php` implementing a `Domain` repository interface, clients for external APIs, framework-specific event listeners.
- **Rule**: This is the **ONLY** layer where framework-specific code (like Doctrine entities, Symfony components) is allowed.

#### `Interface` Layer
- The entry point for external clients (e.g., HTTP API, CLI).
- Contains API controllers, CLI commands, and message consumers.
- **Rule**: This layer's responsibility is to translate incoming requests into Commands or Queries, dispatch them to the bus, and format the response from the returned result object (`QueryResult` or `CommandResult`). It should contain minimal logic.

## 3. CQRS Implementation

- CQRS is strictly enforced to separate write operations (Commands) from read operations (Queries).
- The implementation relies on custom result wrappers: `Common/Application/QueryResult.php` and `Common/Application/CommandResult.php`.
- **Rule**: Use `readonly class` for all DTOs (Commands, Queries, Results). If a class cannot be fully readonly (e.g., for Doctrine entities), use `readonly` on all its properties.

#### Commands (Write Model)
- Represent an intent to change the system's state. Located in `src/Module/{ModuleName}/Application/Command/`.
- Command Handlers are located in `src/Module/{ModuleName}/Application/Command/{CommandName}CommandHandler.php`.

- **Sync Commands**:
    - Dispatched to the `messenger.bus.command` bus.
    - Used for operations requiring an immediate, consistent result (e.g., creating a user, adding a product to a cart).
    - **Rule**: Sync command handlers **MUST** return a `CommandResult` object. On success, this typically contains the ID of the created/modified aggregate, e.g., `CommandResult::success($newUserId)`.

- **Async Commands**:
    - Dispatched to the `messenger.bus.async_command` bus.
    - Used for operations that can be deferred or are long-running (e.g., sending a welcome email, generating a report).
    - **Rule**: Async command handlers **MUST** have a `void` return type. They do not return any data to the original caller.

#### Queries (Read Model)
- Represent a request for data. Located in `src/Module/{ModuleName}/Application/Query/`.
- Query Handlers are located in `src/Module/{ModuleName}/Application/Query/{QueryName}QueryHandler.php`.
- **Rule**: Queries **MUST NOT** change the state of the system in any way.
- **Rule**: Query handlers **MUST** return a `QueryResult` object containing the requested data, e.g., `QueryResult::success($userData)`.
- **Rule**: For performance, query handlers should bypass the Domain/Aggregates and query the database directly (e.g., using Doctrine DQL) to build read-model DTOs tailored for the client.

## 4. Database & Persistence

- **Rule**: Each module has its own dedicated database and its own migrations in `migrations/{ModuleName}/`. This enforces strict module separation.
- **Rule**: Cross-module database queries are strictly forbidden.
- Repository interfaces are defined in the `Domain` layer.
- Doctrine implementations of these repositories reside in the `Infrastructure` layer.

## 5. Coding Style & Conventions

- `declare(strict_types=1);` is mandatory in all PHP files.
- All classes and methods should be `final` by default, unless explicitly designed for extension.
- Use constructor property promotion with `readonly` access modifiers wherever possible.
- Adhere to PSR-12 coding standards.

## 6. Validation & DTOs

The backend uses a specific pattern for handling and validating incoming request data, leveraging Symfony's Value Resolvers and Validator components.

-   **Validation DTOs**: For every complex input (e.g., creating a product), a dedicated Data Transfer Object (DTO) is created in the `Application/DTO/Validation/` directory of the relevant module.
    -   **Rule**: These DTOs use PHP attributes (`#[Assert\*]`) from the Symfony Validator component to define the validation rules for each property.
    -   **Rule**: All validation DTOs must extend `App\Common\Application\DTO\AbstractValidationDTO`, which provides helper methods for managing validation errors.

-   **Value Resolvers**: For each validation DTO, a corresponding `ValueResolver` is created in the `Application/ValueResolver/{ControllerName}/` directory.
    -   **Rule**: The resolver is responsible for creating the DTO instance from the incoming `Request` data, running the validator on it, and attaching any errors to the DTO.
    -   **Rule**: The controller action then receives the fully populated and validated DTO as an argument, ready for immediate use.

-   **Controller Flow**:
    1.  A request hits a controller endpoint.
    2.  The corresponding Value Resolver is triggered.
    3.  The resolver creates a DTO, validates it, and passes it to the controller method.
    4.  The controller checks `$dto->hasErrors()`.
        -   If `true`, it returns a `400 Bad Request` response with the validation errors.
        -   If `false`, it maps the DTO to a Command or Query and dispatches it to the bus.

This pattern keeps controllers clean and thin, centralizes validation logic on the DTOs, and automates the process of request-to-DTO transformation.

## 7. Standardized API Response

- **Rule**: All API endpoints must return a consistent JSON response structure to ensure a predictable experience for clients.

- **Success Response**:
    ```json
    {
      "success": true,
      "data": { ... }
    }
    ```
    -   `success`: Always `true`.
    -   `data`: Contains the requested data. Can be an object or an array.

- **Error Response**:
    ```json
    {
      "success": false,
      "message": "A human-readable error message.",
      "errors": { ... }
    }
    ```
    -   `success`: Always `false`.
    -   `message`: (Optional) A general, user-friendly error message.
    -   `errors`: (Optional) A key-value object containing detailed validation errors, where the key is the field name and the value is the error message.

## 8. The `Common` Directory

- The `src/Common/` directory contains code that is shared across all modules. It is the project's shared kernel.
- **Rule**: This directory should only contain code that is genuinely reusable and not specific to any single business domain (module).
- Examples of common code include:
    -   The base `QueryResult` and `CommandResult` classes.
    -   The `AbstractValidationDTO`.
    -   Interfaces for cross-cutting concerns like caching (`CacheProxyInterface`) and serialization (`JsonSerializerInterface`).
    -   The core Query and Command bus implementations.

## 9. Logging

- **Rule**: The application must log all critical errors and exceptions to provide visibility for debugging.
- The `Psr\Log\LoggerInterface` is injected into services, command handlers, and query handlers.
- **Rule**: When catching an exception, log it with a descriptive message and a rich context array. The context should include:
    -   Input parameters (e.g., command/query properties).
    -   The exception message.
    -   The exception class.
    -   The stack trace.
- This ensures that developers have all the necessary information to diagnose a problem without needing to reproduce it.
