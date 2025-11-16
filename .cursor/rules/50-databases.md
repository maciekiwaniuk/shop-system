# 5. Database Architecture & Strategy

This document outlines the database strategy for the entire system, covering the separation of data, replication, and environments.

## Database per Service/Module

A core principle of the architecture is the "Database per Service" pattern. Each module within the monolith and each microservice owns its data and has its own dedicated database. This enforces loose coupling and allows each service/module to evolve its schema independently.

- **Technology**: The primary relational database is **MySQL version 9.1**.
- **Rule**: Direct cross-database queries are strictly forbidden. Communication between services/modules must go through their public APIs or message queues, never by directly accessing another's database.

### Database List

- **`shop_system_auth`**: Belongs to the `Auth` module. Manages users, credentials, and roles.
- **`shop_system_commerce`**: Belongs to the `Commerce` module. Manages clients, products, and orders.
- **`shop_system_payments`**: Belongs to the `payments` microservice. Manages payers and transactions.

## Testing Environment Databases

For test automation, each service/module has a separate, ephemeral database that is used exclusively for running automated tests (unit, integration, application).

- **`shop_system_auth_test`**
- **`shop_system_commerce_test`**
- **`shop_system_payments_test`**

**Rule**: These databases are managed by the CI/CD pipeline. They are created, migrated, and seeded during the test setup and should be considered volatile.

## Data Replication and Shared Identifiers

While the databases are separate, certain entities represent the same conceptual "thing" across different bounded contexts. Data is replicated between them, and a consistent ID is used to link these records.

- **Primary Identifier**: A `UUID` is used as the primary identifier for these shared entities. This ID is generated once and then propagated across services.

### Replication Flows

#### User → Client → Payer
This entity represents a person interacting with the system.
1.  **`User` (`shop_system_auth`)**: When a person registers an account, a `User` record is created in the `Auth` module's database. A new UUID is generated for this user.
2.  **`Client` (`shop_system_commerce`)**: Through an asynchronous event (`UserRegisteredEvent`), a corresponding `Client` record is created in the `Commerce` module's database, reusing the same UUID from the `User` record.
3.  **`Payer` (`shop_system_payments`)**: When a user/client initiates their first transaction, a `Payer` record is created in the `payments` microservice's database, again reusing the same UUID.

#### Order → Transaction
This entity represents a purchase.
1.  **`Order` (`shop_system_commerce`)**: When a client places an order, an `Order` record is created in the `Commerce` module. A new UUID is generated for this order.
2.  **`Transaction` (`shop_system_payments`)**: As part of the order creation process, a synchronous API call is made to the `payments` microservice, which creates a `Transaction` record. This `Transaction` reuses the same UUID generated for the `Order`.

This strategy of replicating data and sharing a consistent ID provides the benefits of service autonomy while still allowing for easy correlation of data across the entire system.

- **Rule**: Use UUIDs for entities where exposing a sequential count could be a sensitive information leak (e.g., number of users, number of orders). Use standard auto-incrementing integer IDs for entities where this is not a concern (e.g., products).

## Caching (Redis)

- **Purpose**: Redis is used as a cache to improve performance for frequently accessed, and infrequently changed, data.
- **Implementation**: A generic `CacheProxyInterface` is defined in `src/Common/Domain/Cache/`, with a Redis implementation `CacheProxy` in `src/Common/Infrastructure/Cache/`.
- **Current Usage**: It is primarily used to cache individual product details. When a product is requested by its slug, the system first checks the Redis cache. If found, the cached data is returned, avoiding a database query. If not, the data is fetched from the database and then stored in Redis for subsequent requests.
- **Rule**: When implementing new queries for data that is a good candidate for caching (read often, written rarely), use the `CacheProxy` to store the results.

## Search (Elasticsearch)

- **Purpose**: Elasticsearch is used to provide a rich, full-text search capability for the product catalog.
- **Implementation**: The `ProductSearchRepositoryInterface` in the Commerce module defines the contract for searching products. The `ProductIndexManager` in `src/Module/Commerce/Infrastructure/Elasticsearch/` provides the concrete implementation.
- **Indexing**:
    - A `ProductIndexingListener` automatically indexes a product in Elasticsearch whenever it is created or updated.
    - A console command is also available to create the index mapping.
- **Searching**: The search implementation uses `match_phrase_prefix` and fuzzy matching (`fuzziness: 'AUTO'`) to provide more relevant and tolerant search results for user queries.
- **Rule**: All product search queries from the frontend search bar must be routed through the Elasticsearch repository to leverage its advanced search features.
