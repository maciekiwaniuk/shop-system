# Frontend Authentication System

This frontend implements a complete authentication and authorization system using Next.js 15, React 19, and TypeScript.

## Features

- **JWT Authentication**: Secure token-based authentication
- **Role-based Authorization**: Support for user and admin roles
- **Protected Routes**: Automatic route protection based on authentication status
- **Persistent Sessions**: Token storage in localStorage
- **API Integration**: Seamless integration with the Symfony backend API

## Setup

1. **Environment Variables**: Create a `.env.local` file in the frontend directory:
   ```
   NEXT_PUBLIC_API_URL=http://localhost/api/v1
   ```

2. **Install Dependencies**:
   ```bash
   npm install
   ```

3. **Run Development Server**:
   ```bash
   npm run dev
   ```

## Architecture

### Authentication Flow

1. **Login/Register**: Users authenticate through forms that call the backend API
2. **Token Storage**: JWT tokens are stored in localStorage and decoded to extract user information
3. **Context Management**: React Context provides authentication state throughout the app
4. **Route Protection**: Protected routes automatically redirect unauthenticated users

### Key Components

- **AuthContext**: Manages authentication state and provides auth methods
- **ProtectedRoute**: Component for protecting routes based on auth requirements
- **AuthService**: Singleton service for token management and API calls
- **ApiService**: Service for making authenticated requests to the backend

### Route Structure

- `/login` - Public login page
- `/register` - Public registration page
- `/(authenticated)/*` - Routes requiring authentication
- `/(admin)/*` - Routes requiring admin authentication

## Usage

### Using Authentication in Components

```tsx
import { useAuth } from '@/contexts/AuthContext';

function MyComponent() {
  const { user, isAuthenticated, isAdmin, logout } = useAuth();
  
  if (!isAuthenticated) {
    return <div>Please log in</div>;
  }
  
  return (
    <div>
      <p>Welcome, {user?.email}</p>
      {isAdmin && <p>You are an administrator</p>}
      <button onClick={logout}>Logout</button>
    </div>
  );
}
```

### Protecting Routes

```tsx
import ProtectedRoute from '@/components/ProtectedRoute';

// Require authentication
<ProtectedRoute requireAuth={true}>
  <MyComponent />
</ProtectedRoute>

// Require admin access
<ProtectedRoute requireAuth={true} requireAdmin={true}>
  <AdminComponent />
</ProtectedRoute>
```

### Making API Calls

```tsx
import { apiService } from '@/lib/api';

// Get products
const response = await apiService.getProducts(0, 10);
if (response.success) {
  console.log(response.data);
}

// Create a product (requires authentication)
const response = await apiService.createProduct({
  name: 'New Product',
  price: 99.99
});
```

## Security Features

- **Token Validation**: Automatic token validation and refresh
- **Route Protection**: Unauthorized users are redirected to login
- **Role-based Access**: Different access levels for users and admins
- **Secure Storage**: Tokens stored securely in localStorage
- **Error Handling**: Comprehensive error handling for authentication failures

## API Integration

The frontend integrates with the Symfony backend API endpoints:

- `POST /api/v1/login` - User authentication
- `POST /api/v1/register` - User registration
- `GET /api/v1/products/*` - Product management
- `GET /api/v1/orders/*` - Order management

All authenticated requests automatically include the JWT token in the Authorization header.

## Development

### Adding New Protected Routes

1. Create your page in the appropriate directory:
   - `src/app/(authenticated)/` for user-only routes
   - `src/app/(admin)/` for admin-only routes

2. The layout files automatically apply protection

### Adding New API Endpoints

1. Add the method to `src/lib/api.ts`
2. Use the `makeRequest` method for authenticated calls
3. Handle the response appropriately in your components

### Customizing Authentication

- Modify `src/lib/auth.ts` for token handling logic
- Update `src/contexts/AuthContext.tsx` for state management
- Customize `src/components/ProtectedRoute.tsx` for route protection logic 