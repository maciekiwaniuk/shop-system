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
