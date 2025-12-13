# Frontend (Next.js) Architecture & Rules

The frontend is a Next.js application written in TypeScript, using Tailwind CSS for styling and Zustand for state management. It communicates with the backend via a RESTful API.

## 0. Runtime & Package Manager

- **Package Manager**: Bun (`oven/bun:1.3-alpine`)
- **Build Runtime**: Bun
- **Production Runtime**: Node.js
- **Lockfile**: `bun.lock` (binary format)

### Two-Stage Docker Build Strategy (Bun → Node)

The Dockerfile uses a 2-stage build:
1. **builder stage (Bun)**: `bun install` + `next build` executed via Bun (`bun --bun ...`)
2. **runtime stage (Node)**: runs the generated Next.js standalone server via `node server.js`

#### Bun Build Crash Fix

Next.js may attempt to prerender its internal `/_global-error` route during build. With Bun + React 19 this can crash with a `useContext`-related error.
To prevent that, the app provides an explicit `src/app/global-error.tsx` error boundary.

### Running Bun Commands Locally

- **Rule**: Do not install Bun directly on the host machine. Use the Docker container to run Bun commands.
- **Rule**: When running Bun commands outside of the development environment (e.g., to regenerate `bun.lock` or add dependencies), use the Docker image:

```bash
# From the project root directory
docker run --rm -v ./frontend:/app -w /app oven/bun:1.3-alpine bun install
docker run --rm -v ./frontend:/app -w /app oven/bun:1.3-alpine bun add <package-name>
```

- **Rule**: The `development/docker-compose.yml` handles running the frontend dev server automatically via the `shop-system-frontend` service.

## 1. Project Structure

The source code is located in `frontend/src/`. Key directories are:

- `app/` - Contains all pages and layouts, following the Next.js App Router conventions.
    - `(pages)/` - Route groups for different sections of the site (e.g., `cart`, `products`).
    - `layout.tsx` - The root layout for the entire application.
    - `page.tsx` - The main homepage component.
    - `api/` - API routes handled by the Next.js backend (serverless functions).

- `components/` - Reusable React components.
    - `ui/` - Generic, stateless UI components (Button, Card, Input). These should be highly reusable and contain no business logic.
    - `layout/` - Components related to the overall page structure (Header, Footer).
    - `product/` - Components specific to the product domain (ProductCard, ProductGrid).
    - `shared/` - Common components that don't fit into other categories (LoadingSpinner).

- `lib/` - Core logic, hooks, and external service integrations.
    - `api/` - Functions for making API calls to the backend monolith. Each file corresponds to a backend module/resource (e.g., `products.ts`, `auth.ts`).
    - `hooks/` - Custom React hooks (e.g., `useDebounce`).
    - `store/` - Zustand store definitions for global state management (`cartStore.ts`, `authStore.ts`).
    - `utils/` - Utility and helper functions.

- `styles/` - Global styles.
- `types/` - TypeScript type definitions, often mirroring backend DTOs.

## 2. Component Strategy

- **Rule**: Create small, focused components.
- **Rule**: Separate presentational components from container components.
    - **UI Components (`components/ui/`)**: Should be purely presentational. They receive props and render UI. They do not fetch data or manage complex state.
    - **Feature Components (`components/product/`, etc.)**: Can contain business logic and state relevant to their feature.
    - **Page Components (`app/**/*.tsx`)**: Assemble feature and UI components to build a full page. They are responsible for fetching initial page data.

## 3. State Management (Zustand)

- **Rule**: Global application state (e.g., shopping cart, user authentication status) is managed with Zustand.
- **Rule**: Store definitions are located in `lib/store/`.
- **Rule**: For local component state that doesn't need to be shared, use React's built-in `useState` and `useReducer` hooks. Do not clutter global stores with local state.

## 4. API Communication

- **Rule**: All communication with the backend API must be handled by the functions in `lib/api/`. Components should not make direct `fetch` calls.
- **Rule**: API client functions should handle request setup (headers, auth tokens) and response parsing.
- **Rule**: Use a consistent pattern for handling loading, error, and data states in components that fetch data. The `swr` or `react-query` libraries are preferred for this, but if not used, manage states manually.
- **Rule**: Type definitions for API payloads and responses are located in `types/` and should always be used.

## 5. Styling (Tailwind CSS)

- **Rule**: Style components using Tailwind CSS utility classes directly in the JSX.
- **Rule**: For combining classes conditionally, use the `cn` utility function from `lib/utils/cn.ts`.
- **Rule**: Avoid writing custom CSS in `styles/globals.css` unless absolutely necessary for base styles or overriding a library's styles. Component-specific styles belong in the component file with Tailwind.

## 6. TypeScript

- **Rule**: Use TypeScript for all new code. Strive for strong type safety.
- **Rule**: Define shared types in the `types/` directory. Types specific to a single component can be co-located with that component.
- **Rule**: Use `interface` or `type` as appropriate. Use `interface` for defining the shape of objects and `type` for unions, intersections, or primitives.

## 7. Coding Style & Conventions

- Adhere to the rules defined in `.eslintrc.config.mjs`. Run the linter to check for issues.
- Use functional components with hooks. Class components are not to be used.
- Name files and components using PascalCase (e.g., `ProductCard.tsx`).
- Name hooks using camelCase with a `use` prefix (e.g., `useDebounce.ts`).
