# 6. Authentication & Authorization

This document outlines the security architecture for the system, covering how users are authenticated (proven who they are) and authorized (what they are allowed to do).

## Authentication: JWT (JSON Web Tokens)

Authentication in the PHP backend is handled using JWTs.

- **Implementation**: The system uses the `lexik/jwt-authentication-bundle`, a robust and secure library for handling JWTs in a Symfony application.
- **Flow**:
    1.  A user submits their credentials (e.g., email and password) to a login endpoint.
    2.  The backend validates these credentials.
    3.  If valid, the `lexik-jwt-authentication-bundle` generates a signed, stateless JWT and returns it to the client.
    4.  The frontend stores this token securely (e.g., in an HttpOnly cookie or secure storage).
    5.  For all subsequent requests to protected endpoints, the frontend must include the JWT in the `Authorization` header as a Bearer token (e.g., `Authorization: Bearer <token>`).
    6.  The backend automatically validates this token on incoming requests to secure the endpoints.

## Authorization: Symfony Voters

Authorization is managed using Symfony's Voter system. Voters are used to implement fine-grained, attribute-based access control on specific resources or actions.

- **How it Works**: A Voter is a class that "votes" on whether the current user has permission to perform a certain action (an "attribute") on a given object (a "subject").
- **Implementation**:
    - Voters are created for each primary domain entity, such as `ProductsVoter` and `OrdersVoter`.
    - They define a set of supported actions (e.g., `CREATE_PRODUCT`, `SHOW_ORDER`).
    - Inside a controller, before performing an action, the code calls `$this->denyAccessUnlessGranted('ACTION', $subject);`.
    - The Voter then implements the logic to determine access. For example, the `OrdersVoter` checks if the current user is an admin OR if the order's client ID matches the current user's ID.
- **Rule**: All new actions on resources that require specific permissions must be protected by a Voter. Do not put authorization logic (e.g., role checks) directly in controllers or services.

## User Context

- To get information about the currently authenticated user (like their ID or roles), the application uses a custom `UserContextInterface`.
- The implementation, `App\Module\Auth\Infrastructure\Security\UserContext`, is a wrapper around Symfony's `Security` service, providing a clean and testable way to access user details throughout the application, especially within Voters.
