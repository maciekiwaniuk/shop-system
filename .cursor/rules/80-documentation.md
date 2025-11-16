# 8. Project & API Documentation

This document outlines the strategy for maintaining both high-level project documentation and detailed API documentation.

## 1. API Documentation (Swagger / OpenAPI)

The backend's REST API is documented using the OpenAPI standard (formerly Swagger). This provides an interactive, machine-readable specification of all available endpoints.

-   **Implementation**: The project uses the `NelmioApiDocBundle` for Symfony to generate the API documentation.
-   **Documentation Strategy**: **YAML First**. To maintain a strict separation of concerns, the OpenAPI specification is defined in YAML files, not in PHP code annotations.
    -   **Rule**: All API documentation, including endpoint definitions, request/response models, and parameters, **must** be written in YAML files (e.g., inside `config/packages/nelmio_api_doc.yaml`).
    -   **Rule**: PHP controller and DTO files **must not** contain any `#[OA]` annotations.
-   **Accessing the Documentation**:
    -   **Swagger UI (Interactive Browser)**: The interactive API documentation can be accessed locally at `/api/doc`.
    -   **OpenAPI JSON Specification**: The raw `swagger.json` file is available at `/api/doc.json`.

## 2. Project Documentation (`README.md`)

The main `README.md` file serves as the high-level entry point for the project, explaining its purpose, architecture, and how to get started.

-   **Asset Storage**: The `docs/` directory is the designated storage location for all static assets that are embedded in the `README.md`.
-   **Contents**: This includes:
    -   Images (`.png`, `.jpg`)
    -   Architectural diagrams
    -   GIFs or short videos demonstrating features.
-   **Rule**: When adding a new visual element to the `README.md`, first add the source file to the `docs/` directory and then link to it using a relative path. This keeps the root directory clean and organized.
