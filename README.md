# 🛍️ Shop System - Modern E-commerce Platform

A **simple e-commerce platform** built with cutting-edge technologies, showcasing advanced software architecture patterns and best practices.

[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.3+-000000?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com)
[![Next.js](https://img.shields.io/badge/Next.js-15.3+-000000?style=for-the-badge&logo=next.js&logoColor=white)](https://nextjs.org)
[![React](https://img.shields.io/badge/React-19.0+-61DAFB?style=for-the-badge&logo=react&logoColor=black)](https://reactjs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.0+-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Docker](https://img.shields.io/badge/Docker-24.0+-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![MySQL](https://img.shields.io/badge/MySQL-9.1+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Elasticsearch](https://img.shields.io/badge/Elasticsearch-8.18+-005571?style=for-the-badge&logo=elasticsearch&logoColor=white)](https://elastic.co)
[![Redis](https://img.shields.io/badge/Redis-7.4+-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)

## 🏗️ Architecture Overview

This project demonstrates **enterprise-level software architecture** with a focus on:

### 🎯 **Domain-Driven Design (DDD)**
- **Bounded Contexts**: Auth, Commerce, Payment modules
- **Rich Domain Models**: Business logic encapsulated in entities
- **Domain Events**: Event-driven architecture for side effects
- **Value Objects**: Immutable business concepts

### 🔄 **Command Query Responsibility Segregation (CQRS)**
- **Command Bus**: Handles write operations with Command/CommandHandler pattern
- **Query Bus**: Handles read operations with Query/QueryHandler pattern
- **Event Sourcing Ready**: Structured for future event sourcing implementation
- **Async Commands**: Background processing for non-critical operations

### 🏛️ **Clean Architecture**
- **Layered Architecture**: Domain → Application → Infrastructure → Interface
- **Hexagonal Architecture**: Business logic isolated from external concerns

### 🧪 **Test-Driven Development (TDD)**
- **E2E Tests**: Full integration testing
- **Unit Tests**: Isolated component testing
- **PHPUnit 12.2**: Latest testing framework

## 🚀 **Key Features**

### 🔐 **Authentication & Authorization**
- **JWT Authentication** with LexikJWTAuthenticationBundle
- **Role-based Access Control** (RBAC)
- **Custom Voters** for fine-grained permissions
- **User Registration & Login** with email verification

### 🛒 **E-commerce Functionality**
- **Product Management**: CRUD operations with validation
- **Order Processing**: Complete order lifecycle
- **Search & Filtering**: Elasticsearch-powered product search
- **Pagination**: Efficient data loading

### 📊 **Performance & Scalability**
- **Redis Caching**: Multi-layer caching strategy
- **Elasticsearch**: Full-text search and analytics
- **Message Queues**: RabbitMQ for async processing
- **Database Optimization**: Doctrine ORM with query optimization

### 🔧 **Developer Experience**
- **Docker Compose**: Complete development environment
- **Code Quality**: PHPStan, PHPCS, Deptrac
- **API Documentation**: Nelmio API Doc Bundle
- **Hot Reload**: Frontend development with Turbopack

## 🛠️ **Technology Stack**

### **Backend**
- **PHP 8.4+**: Latest PHP with modern features
- **Symfony 7.3**: Enterprise PHP framework
- **Doctrine ORM 3.5**: Advanced object-relational mapping
- **MySQL 9.1**: Primary database
- **Elasticsearch 8.18**: Search and analytics engine
- **Redis 7.4**: Caching and session storage
- **RabbitMQ 4.1**: Message queuing system

### **Frontend**
- **Next.js 15.3**: React framework with SSR
- **React 19.0**: Latest React with concurrent features
- **TypeScript 5.0**: Type-safe JavaScript
- **Tailwind CSS 4**: Utility-first CSS framework
- **Headless UI**: Accessible UI components

### **DevOps & Tools**
- **Docker**: Containerized development environment
- **PHPUnit 12.2**: Testing framework
- **PHPStan 2.1**: Static analysis
- **Deptrac 3.0**: Architecture enforcement
- **MailHog**: Email testing

## 📁 **Project Structure**

```
shop-system/
├── src/                          # Backend source code
│   ├── Common/                   # Shared components
│   │   ├── Application/         # Application layer
│   │   │   ├── Bus/            # Command/Query buses
│   │   │   ├── DTO/            # Data Transfer Objects
│   │   │   └── Security/       # Security components
│   │   ├── Domain/             # Domain layer
│   │   ├── Infrastructure/     # Infrastructure layer
│   │   └── Interface/          # Interface layer
│   └── Module/                 # Business modules
│       ├── Auth/               # Authentication module
│       ├── Commerce/           # E-commerce module
│       └── Payment/            # Payment processing module
├── frontend/                    # Next.js frontend
├── tests/                       # Tests
├── docker/                      # Docker configuration
└── config/                      # Symfony configuration
```

## 🧪 **Testing**

### **Test Coverage**
- **E2E Tests**: Full application testing
- **Unit Tests**: Component isolation
- **Integration Tests**: Service integration

### **Code Quality**
```bash
# Static Analysis
docker compose exec shop-system-backend vendor/bin/phpstan analyse

# Code Style
docker compose exec shop-system-backend vendor/bin/phpcs

# Architecture Enforcement
docker compose exec shop-system-backend vendor/bin/deptrac analyse
```

## 📚 **API Documentation**

### **Interactive Documentation**
- **Swagger UI**: http://localhost/api/doc
- **OpenAPI JSON**: http://localhost/api/doc.json
- **Complete Documentation**: See `API_DOCUMENTATION.md`

### **Authentication**
```http
POST /api/v1/register    # Register new user
POST /api/v1/login       # Authenticate user
```

### **Products**
```http
GET    /api/v1/products/get-paginated  # List products (Admin)
POST   /api/v1/products/create         # Create product (Admin)
GET    /api/v1/products/show/{slug}    # Get product by slug
PUT    /api/v1/products/update/{id}    # Update product (Admin)
DELETE /api/v1/products/delete/{id}    # Delete product (Admin)
GET    /api/v1/products/search         # Search products
```

### **Orders**
```http
GET  /api/v1/orders/get-paginated      # List orders
POST /api/v1/orders/create             # Create order
GET  /api/v1/orders/show/{uuid}        # Get order by UUID
POST /api/v1/orders/change-status/{uuid} # Update status (Admin)
```

### **Features**
- **JWT Authentication**: Bearer token required for protected endpoints
- **Role-based Access**: Admin and client permissions
- **Validation**: Comprehensive input validation
- **Error Handling**: Consistent error response format
- **Pagination**: Efficient data loading
- **Search**: Elasticsearch-powered product search

## 🏗️ **Architecture Patterns**

### **CQRS Implementation**
```php
// Command Pattern
class CreateProductCommand implements SyncCommandInterface
{
    public function __construct(public CreateProductDTO $dto) {}
}

// Query Pattern
class SearchProductsByPhraseQuery implements QueryInterface
{
    public function __construct(public string $phrase) {}
}
```

### **Domain Events**
```php
class ProductCreatedEvent extends Event
{
    public function __construct(public ProductDTO $product) {}
}
```

### **Repository Pattern**
```php
interface ProductRepositoryInterface
{
    public function save(Product $product, bool $flush = false): int;
    public function findById(int $id): ?Product;
    public function findBySlug(string $slug): ?Product;
}
```

## 🔧 **Development Workflow**

### **Code Standards**
- **PSR-12**: PHP coding standards
- **Slevomat Coding Standard**: Additional rules
- **Type Safety**: Strict types throughout
- **Documentation**: PHPDoc for all public methods

## 📈 **Performance Features**

### **Caching Strategy**
- **Redis**: Application cache
- **Elasticsearch**: Search result cache

### **Database Optimization**
- **Indexed Queries**: Optimized database queries
- **Connection Pooling**: Efficient database connections
- **Query Optimization**: Doctrine query optimization
- **Soft Deletes**: Data integrity preservation

## 🔒 **Security Features**

### **Authentication**
- **JWT Tokens**: Stateless authentication
- **Rate Limiting**: API protection

### **Authorization**
- **Role-based Access**: User roles and permissions
- **Custom Voters**: Fine-grained access control
- **API Security**: CORS protection
- **Input Validation**: Comprehensive validation

## 🌟 **Key Highlights**

### **Enterprise-Ready**
- **Scalable Architecture**: Designed for growth
- **Microservices Ready**: Modular design
- **Monitoring Ready**: Logging and metrics

### **Developer Experience**
- **Hot Reload**: Fast development cycles
- **Comprehensive Testing**: 100% test coverage goal
- **Code Quality Tools**: Automated quality checks

# API Documentation

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

