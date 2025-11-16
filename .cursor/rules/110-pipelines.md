# 11. CI/CD Pipelines (GitHub Actions)

This document describes the Continuous Integration (CI) pipeline configured with GitHub Actions. The workflow files are located in the `github/_workflows/` directory, with reusable components in `github/actions/`.

## Main Workflow: `ci.yaml`

- **Trigger**: The CI pipeline is triggered on every `push` and `pull_request` to the `main` branch.
- **Strategy**: The pipeline is designed for efficiency and parallelism. It consists of multiple jobs that run in a specific order, leveraging dependencies (`needs`) and build artifacts.

### Job Execution Flow

1.  **`build` (Initial Setup)**
    - This job runs first.
    - It checks out the code, sets up the correct PHP version ('8.4'), and caches Composer dependencies based on the `composer.lock` file.
    - It runs `composer install` to create the `vendor` directory.
    - **Key Output**: It uploads the `vendor` directory and `composer.lock` as a build artifact named `build-files`. This artifact is then downloaded by subsequent jobs, avoiding the need to run `composer install` repeatedly.

2.  **Parallel Static Analysis & Unit Test Jobs**
    - The following jobs all run in parallel as soon as the `build` job is complete (`needs: build`).
    - They all use the `common-minimal-setup` reusable action to prepare the environment.
        - **`coding-standards`**: Runs `phpcs` to check for PSR-12 compliance.
        - **`architecture`**: Runs `deptrac` to enforce architectural boundaries (e.g., preventing modules from coupling incorrectly).
        - **`static-analysis`**: Runs `phpstan` for static code analysis to find potential bugs.
        - **`unit-tests`**: Runs fast, isolated unit tests using `phpunit --group unit`.

3.  **Integration & Application Test Jobs**
    - These jobs run only after all the static analysis and unit test jobs have passed successfully.
    - They use the `common-total-setup` reusable action to prepare a much more complex environment.
    - They also spin up service containers directly in the job runner for dependencies (`mysql`, `redis`, `elasticsearch`, `rabbitmq`).
        - **`integration-tests`**: Runs tests that require database or other service connections using `phpunit --group integration`.
        - **`application-tests`**: Runs end-to-end application tests using `phpunit --group application`.

---

## Reusable Actions

The pipeline makes extensive use of composite actions to avoid code duplication.

### `common-minimal-setup`
- **Purpose**: Provides the basic environment for jobs that only need to lint or run simple scripts.
- **Steps**:
    1.  Sets up PHP.
    2.  Downloads the `build-files` artifact (containing the `vendor` directory).
    3.  Makes the scripts in `vendor/bin/` executable.

### `common-total-setup`
- **Purpose**: Provides a fully configured environment for jobs that need to interact with databases and other services (integration/application tests).
- **Steps**:
    1.  Sets up PHP with all required extensions (pdo, redis, amqp, etc.).
    2.  Installs the Redis CLI.
    3.  Downloads the `build-files` artifact.
    4.  Makes `vendor/bin/` scripts executable.
    5.  Generates JWT keys for authentication tests.
    6.  **Waits for Services**: Critically, it includes a wait-for-it script that polls MySQL, Redis, and Elasticsearch to ensure they are healthy before proceeding.
    7.  **Database Initialization**: Creates all the necessary test databases (`shop_system_auth_test`, `shop_system_commerce_test`, etc.).
    8.  **Migrations**: Runs `doctrine:migrations:migrate` to set up the schema.
    9.  **Elasticsearch**: Creates the product index required for tests.
