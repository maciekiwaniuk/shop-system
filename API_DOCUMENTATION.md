# 🚀 Shop System API Documentation

## 📋 Table of Contents

- [Overview](#overview)
- [Authentication](#authentication)
- [Base URL](#base-url)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Endpoints](#endpoints)
  - [Authentication](#authentication-endpoints)
  - [Products](#product-endpoints)
  - [Orders](#order-endpoints)
- [Examples](#examples)
- [Rate Limiting](#rate-limiting)
- [Support](#support)

## 📖 Overview

The Shop System API is a modern e-commerce platform built with Symfony 7.3, featuring:

- **JWT Authentication**: Secure token-based authentication
- **Product Management**: Full CRUD operations with search capabilities
- **Order Management**: Order creation and status management
- **Role-based Access Control**: Admin and client permissions
- **CQRS Architecture**: Command and Query Responsibility Segregation
- **Elasticsearch Integration**: Advanced product search
- **Redis Caching**: Performance optimization

## 🔐 Authentication

All protected endpoints require a valid JWT token in the Authorization header:

```http
Authorization: Bearer <your-jwt-token>
```

### Getting a Token

1. **Register** a new account via `/api/v1/register`
2. **Login** via `/api/v1/login` to receive a JWT token
3. Include the token in subsequent requests

## 🌐 Base URL

```
http://localhost/api/v1
```

## 📤 Response Format

All API responses follow a consistent format:

### Success Response
```json
{
    "success": true,
    "message": "Optional success message",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## ❌ Error Handling

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 202 | Accepted (Async operations) |
| 400 | Bad Request (Validation errors) |
| 401 | Unauthorized (Invalid/missing token) |
| 403 | Forbidden (Insufficient permissions) |
| 404 | Not Found |
| 500 | Internal Server Error |

### Common Error Responses

#### Validation Error (400)
```json
{
    "success": false,
    "errors": {
        "email": ["Email must be valid."],
        "password": ["Password cannot be blank."]
    }
}
```

#### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid JWT Token"
}
```

#### Forbidden (403)
```json
{
    "success": false,
    "message": "Access Denied."
}
```

## 🔗 Endpoints

### Authentication Endpoints

#### POST /api/v1/register
Register a new user account.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "name": "John",
    "surname": "Doe"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Successfully registered.",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
    }
}
```

#### POST /api/v1/login
Authenticate user and receive JWT token.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Product Endpoints

#### GET /api/v1/products/get-paginated
Retrieve a paginated list of products (Admin only).

**Query Parameters:**
- `offset` (integer, required): Number of items to skip
- `limit` (integer, required): Number of items to return

**Response (200):**
```json
{
    "success": true,
    "data": {
        "products": [
            {
                "id": 1,
                "name": "iPhone 15 Pro",
                "price": 999.99,
                "slug": "iphone-15-pro",
                "createdAt": "2024-01-15T10:30:00+00:00",
                "updatedAt": "2024-01-15T10:30:00+00:00"
            }
        ],
        "total": 100,
        "offset": 0,
        "limit": 10
    }
}
```

#### POST /api/v1/products/create
Create a new product (Admin only).

**Request Body:**
```json
{
    "name": "iPhone 15 Pro",
    "price": 999.99
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Successfully created product.",
    "data": {
        "productId": 1
    }
}
```

#### GET /api/v1/products/show/{slug}
Retrieve a product by its slug.

**Path Parameters:**
- `slug` (string, required): Product slug

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "iPhone 15 Pro",
        "price": 999.99,
        "slug": "iphone-15-pro",
        "createdAt": "2024-01-15T10:30:00+00:00",
        "updatedAt": "2024-01-15T10:30:00+00:00"
    }
}
```

#### PUT /api/v1/products/update/{id}
Update an existing product (Admin only).

**Path Parameters:**
- `id` (integer, required): Product ID

**Request Body:**
```json
{
    "name": "iPhone 15 Pro Max",
    "price": 1199.99
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Successfully updated product."
}
```

#### DELETE /api/v1/products/delete/{id}
Delete a product (Admin only).

**Path Parameters:**
- `id` (integer, required): Product ID

**Response (200):**
```json
{
    "success": true,
    "message": "Successfully deleted product."
}
```

#### GET /api/v1/products/search
Search products by phrase.

**Query Parameters:**
- `phrase` (string, required): Search phrase (2-100 characters)

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "iPhone 15 Pro",
            "price": 999.99,
            "slug": "iphone-15-pro",
            "createdAt": "2024-01-15T10:30:00+00:00",
            "updatedAt": "2024-01-15T10:30:00+00:00"
        }
    ]
}
```

### Order Endpoints

#### GET /api/v1/orders/get-paginated
Retrieve a paginated list of orders.

**Query Parameters:**
- `cursor` (string, optional): Cursor for pagination (UUID)
- `limit` (integer, required): Number of items to return

**Response (200):**
```json
{
    "success": true,
    "data": {
        "orders": [
            {
                "id": 1,
                "uuid": "550e8400-e29b-41d4-a716-446655440000",
                "status": "pending",
                "totalPrice": 1999.98,
                "createdAt": "2024-01-15T10:30:00+00:00",
                "updatedAt": "2024-01-15T10:30:00+00:00"
            }
        ],
        "total": 50,
        "cursor": "550e8400-e29b-41d4-a716-446655440000",
        "limit": 10
    }
}
```

#### GET /api/v1/orders/show/{uuid}
Retrieve an order by its UUID.

**Path Parameters:**
- `uuid` (string, required): Order UUID

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "status": "pending",
        "totalPrice": 1999.98,
        "createdAt": "2024-01-15T10:30:00+00:00",
        "updatedAt": "2024-01-15T10:30:00+00:00"
    }
}
```

#### POST /api/v1/orders/create
Create a new order.

**Request Body:**
```json
{
    "products": [
        {
            "id": 1,
            "quantity": 2,
            "pricePerPiece": 999.99
        },
        {
            "id": 2,
            "quantity": 1,
            "pricePerPiece": 799.99
        }
    ]
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Successfully created order."
}
```

#### POST /api/v1/orders/change-status/{uuid}
Update the status of an order (Admin only).

**Path Parameters:**
- `uuid` (string, required): Order UUID

**Request Body:**
```json
{
    "status": "paid"
}
```

**Response (202):**
```json
{
    "success": true,
    "message": "Successfully queued update status of order."
}
```

## 📝 Examples

### Complete Authentication Flow

```bash
# 1. Register a new user
curl -X POST http://localhost/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123",
    "name": "John",
    "surname": "Doe"
  }'

# Response includes JWT token
# {
#   "success": true,
#   "message": "Successfully registered.",
#   "data": {
#     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
#   }
# }

# 2. Use token for authenticated requests
curl -X GET http://localhost/api/v1/products/search?phrase=iphone \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
```

### Product Management (Admin)

```bash
# Create a product
curl -X POST http://localhost/api/v1/products/create \
  -H "Authorization: Bearer <admin-token>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "MacBook Pro M3",
    "price": 2499.99
  }'

# Update a product
curl -X PUT http://localhost/api/v1/products/update/1 \
  -H "Authorization: Bearer <admin-token>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "MacBook Pro M3 Max",
    "price": 3499.99
  }'

# Delete a product
curl -X DELETE http://localhost/api/v1/products/delete/1 \
  -H "Authorization: Bearer <admin-token>"
```

### Order Management

```bash
# Create an order
curl -X POST http://localhost/api/v1/orders/create \
  -H "Authorization: Bearer <user-token>" \
  -H "Content-Type: application/json" \
  -d '{
    "products": [
      {
        "id": 1,
        "quantity": 2,
        "pricePerPiece": 999.99
      }
    ]
  }'

# Change order status (Admin)
curl -X POST http://localhost/api/v1/orders/change-status/550e8400-e29b-41d4-a716-446655440000 \
  -H "Authorization: Bearer <admin-token>" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "paid"
  }'
```

## ⚡ Rate Limiting

The API implements rate limiting to prevent abuse:

- **Authentication endpoints**: 5 requests per minute
- **Product endpoints**: 100 requests per minute
- **Order endpoints**: 50 requests per minute

When rate limit is exceeded, you'll receive a `429 Too Many Requests` response.

## 🛡️ Security

### JWT Token Security

- Tokens expire after 24 hours
- Use HTTPS in production
- Store tokens securely on the client side
- Never expose tokens in client-side code

### Input Validation

All inputs are validated using Symfony's validation framework:

- **Email**: Must be valid email format and unique
- **Password**: Minimum 2 characters, maximum 100
- **Names**: Minimum 2 characters, maximum 100
- **Prices**: Must be positive numbers
- **Quantities**: Must be positive integers

### Authorization

The API uses role-based access control:

- **Admin**: Full access to all endpoints
- **Client**: Limited access to products and own orders
- **Guest**: Access to public product information only

## 🔧 Development

### Local Development

1. Start the Docker environment:
   ```bash
   docker compose up -d
   ```

2. Access the API documentation:
   - **Swagger UI**: http://localhost/api/doc
   - **JSON Schema**: http://localhost/api/doc.json

3. Run tests:
   ```bash
   docker compose exec shop-system-backend php vendor/bin/phpunit
   ```
