# Shop System

A **simple e-commerce system** built with modern, popular technologies, showcasing advanced software architecture patterns and best practices.

This project is a sandbox for experimenting and learning new techniques and technologies. It’s more of an ongoing project rather than something finished. It's also a way to showcase work and coding skills. Not everything is optimized for maximum efficiency as it would be in a commercial project. For instance, communication between the payment microservice and the monolith is intentionally implemented using mixed sync and async communication to test and learn their pros and cons.

## Project Evolution

At the beginning, this project was a simple monolith to learn Symfony. Later, it evolved to a modular monolith, and then incorporated hexagonal architecture as my experience and curiosity grew.

## Technology Stack & Development Environment

### Core Technologies
- **Backend**: PHP, Symfony, FrankenPHP, PHPUnit
- **Payments Microservice**: Go, Gin
- **Frontend**: TypeScript, React, Next.js, Bun, Tailwind CSS
- **Databases & Storage**: MySQL, Elasticsearch, Redis
- **Messaging**: RabbitMQ
- **Architecture**: Microservices, CQRS, Hexagonal Architecture, DDD

### Development & Operations
- **Containerization**: Docker. The local development environment is managed entirely with Docker Compose, with configuration located in the `development/` directory.
- **Orchestration**: Kubernetes. The configuration for local deployment using Minikube is located in the `k8s/` directory.
- **Local Email Testing**: MailHog
- **CI/CD & Scripts**: The `scripts/` directory contains various utility scripts. The `build-and-push-image.sh` script is used to build and push Docker images to Docker Hub. This is a manual step because the repository is public on GitHub, and therefore requires passing `DOCKERHUB_USERNAME` and `DOCKERHUB_PASSWORD` environment variables manually for security.

### Pre-development Checks
- **Rule**: Before starting any development task, always check the versions of key technologies to ensure compatibility and context awareness.
    - Check `development/docker-compose.yml` for service versions (PHP, MySQL, Redis, etc.).
    - Check `composer.json` for backend PHP dependencies.
    - Check `frontend/package.json` for frontend JavaScript dependencies.
    - Check `microservices/payments/go.mod` for Go microservice dependencies.

### Maintaining Cursor Rules
- **Rule**: Cursor rules must be kept up to date and reflect the current state of the project.
    - When making significant changes that affect architecture, patterns, workflows, or project structure, automatically update the relevant cursor rules files.
    - Examples of changes requiring rule updates:
        - Adding or removing services, modules, or microservices
        - Changing communication patterns (sync/async, messaging, APIs)
        - Modifying deployment or development workflows
        - Introducing new technologies or frameworks
        - Changing architectural patterns or best practices
        - Updates to configuration files or directory structures
    - Cursor rules are located in `.cursor/rules/` and are numbered to indicate priority and category.
    - Keep documentation accurate to ensure AI assistance remains effective and aligned with project standards.

## Project Overview
The main application is built in PHP with Symfony as a modular monolith. The payments microservice is written in Go using the Gin framework. Both the monolith and the payments microservice use ports and adapters architecture and CQRS.

- **Backend**: Modular monolith with modules (Auth, Commerce) under `src/Module/`.
    - **Modules & Bounded Contexts**: Each module or microservice has its own context. For example, the same entity is a `user` in the auth service, a `client` in the clients module, and a `payer` in the payments service. Records are replicated throughout the system. Each module has its own separate database, which prepares the monolith for easier separation into microservices.
    - **Data Replication**: Data replication is done only when necessary. For example, a guest purchase creates records only in the payments and clients modules. An account creation triggers record creation in the user module.
    - **Frameworks**: Uses Symfony for dependency injection, Doctrine for ORM, and Messenger for command buses.

- **Microservices**: Payments microservice in Go under `microservices/payments/`, handling transactions. It also follows CQRS and Hexagonal Architecture principles.

- **Frontend**: Next.js under `frontend/`, with components, hooks, and API calls to the backend.

- **Key Patterns**:
    - **CQRS**:
        - **Queries**: Handle read operations (e.g., search products, get order details).
        - **Sync Commands**: Handle write operations that need to be executed instantly (e.g., create a product, update order status).
        - **Async Commands**: For operations that can be delayed or might take time (e.g., sending email notifications).
    - **Hexagonal Architecture (Ports and Adapters)**: Ports (interfaces) are in the domain/application layers, and adapters are in the infrastructure layer.
    - **Domain-Driven Design (DDD)**: Uses entities, value objects, aggregates, and repositories in the domain layer.

- **Goals**: Enforce modularity, testability, and scalability. This is a showcase, so prioritize best practices over quick hacks.
