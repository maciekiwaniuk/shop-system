# 7. Testing Strategy

This document outlines the project's comprehensive testing strategy, which includes static analysis, code quality checks, and a multi-layered automated testing approach.

## 1. Static Analysis & Quality Tools (Backend)

Before running any tests, the CI pipeline runs a suite of static analysis tools to catch common errors and enforce coding standards early.

-   **`PHP_CodeSniffer` (`phpcs`)**: Enforces PSR-12 coding standards to maintain a consistent code style across the project.
-   **`PHPStan`**: A static analysis tool that scans the codebase for potential bugs, type errors, and other issues without running the code.
-   **`Deptrac`**: Enforces architectural rules, such as preventing modules from having direct dependencies on each other, which is crucial for maintaining the modular monolith structure.

## 2. Automated Testing Philosophy (The Testing Pyramid)

The project follows the principles of the testing pyramid, with a heavy focus on fast, isolated unit tests, a good number of integration tests, and a smaller set of end-to-end application tests.

### Test Directory Structure
-   **Rule**: The `tests/` directory mirrors the structure of the `src/` directory. For example, a test for `src/Module/Commerce/Application/Command/CreateOrderCommandHandler.php` would be located at `tests/Module/Commerce/Application/Command/CreateOrderCommandHandlerTest.php`.

### Test Grouping
-   **Rule**: Every test class **must** be assigned to a group using a PHP attribute: `#[Group('unit')]`, `#[Group('integration')]`, or `#[Group('application')]`.
-   **Usage**: These groups are used by the `tests.sh` script and the CI pipeline (`github/_workflows/ci.yaml`) to run specific test suites in the correct environments.

### Test Base Classes
To reduce boilerplate and provide common setup, all tests must extend one of the abstract base classes:
-   `AbstractUnitTestCase`: For unit tests.
-   `AbstractIntegrationTestCase`: For integration tests, provides setup for a real database connection.
-   `AbstractApplicationTestCase`: For application tests, provides helpers for making API requests and authenticating users for end-to-end tests.

---

## 3. Test Types & Scopes

### Unit Tests `#[Group('unit')]`
-   **Purpose**: To test a single class or method in complete isolation.
-   **Scope**: All external dependencies (repositories, services, event dispatchers, etc.) **must** be mocked. These tests should be very fast and should not touch the database or any other external service.
-   **Location**: Typically in the `Domain/` and `Application/` subdirectories within `tests/Module/{ModuleName}/`.

### Integration Tests `#[Group('integration')]`
-   **Purpose**: To test a class with its direct, real infrastructure dependencies. This is often used to test database repository implementations.
-   **Scope**: These tests connect to a real test database. External microservices or APIs should still be mocked.
-   **Location**: Typically in the `Infrastructure/` subdirectory within `tests/Module/{ModuleName}/`.

### Application Tests `#[Group('application')]`
-   **Purpose**: To test a full business flow or API endpoint from end-to-end.
-   **Scope**: These tests simulate a real user or client. They make actual HTTP requests to the application and assert the responses. They use the full stack, including a real test database, but should mock calls to external microservices (like the payments service).
-   **Location**: Typically in the `Interface/` subdirectory within `tests/Module/{ModuleName}/`.

## 4. Future Plans

-   **Backend**: The testing strategy for the backend is well-established. New features should continue to follow these patterns.
-   **Go Microservice**: Tests will be added in the future. The same principles (unit, integration) will apply, using Go's native testing packages.
-   **Frontend**: Tests will be added in the future, likely using a framework like Jest for unit tests and Playwright for end-to-end tests.
