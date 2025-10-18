# Shop System

A **simple e-commerce system** built with modern, popular technologies, showcasing advanced software architecture patterns and best practices.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PHPUnit](https://img.shields.io/badge/PHPUnit-4C5B5C?style=for-the-badge&logo=phpunit&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Kubernetes](https://img.shields.io/badge/Kubernetes-326CE5?style=for-the-badge&logo=kubernetes&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Elasticsearch](https://img.shields.io/badge/Elasticsearch-005571?style=for-the-badge&logo=elasticsearch&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white)
![MailHog](https://img.shields.io/badge/MailHog-FF6B6B?style=for-the-badge&logo=mail&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-3178C6?style=for-the-badge&logo=typescript&logoColor=white)
![React](https://img.shields.io/badge/React-61DAFB?style=for-the-badge&logo=react&logoColor=black)
![Next.js](https://img.shields.io/badge/Next.js-000000?style=for-the-badge&logo=nextdotjs&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Golang](https://img.shields.io/badge/Go-00ADD8?style=for-the-badge&logo=go&logoColor=white)
![Microservices](https://img.shields.io/badge/Microservices-FF6B6B?style=for-the-badge&logo=microservices&logoColor=white)

### CQRS Implementation
- **Queries** handle read operations (search products, get order details)
- **Sync Commands** handle write operations (create product, update order status)
- **Async Commands** for non-critical operations (email notifications)

## Development

### Installation

**Start the development environment**
```bash
cd development && ./build.sh
```

**Access the application**
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost/api/v1
- **API Documentation**: http://localhost/api/doc
- **RabbitMQ Management**: http://localhost:15672
- **MailHog**: http://localhost:8025

## API Overview

The API follows RESTful principles with consistent response formats. All protected endpoints require a JWT token in the Authorization header.

### Authentication
```http
POST /api/v1/register    # Create account
POST /api/v1/login       # Get JWT token
```

### Products
```http
GET    /api/v1/products/get-paginated  # List products
POST   /api/v1/products/create         # Create product (admin)
GET    /api/v1/products/show/{slug}    # Get product details
PUT    /api/v1/products/update/{id}    # Update product (admin)
DELETE /api/v1/products/delete/{id}    # Delete product (admin)
GET    /api/v1/products/search         # Search products
```

### Orders
```http
GET  /api/v1/orders/get-paginated        # List orders
POST /api/v1/orders/create               # Create order
GET  /api/v1/orders/show/{uuid}          # Get order details
POST /api/v1/orders/change-status/{uuid} # Update status (admin)
```

**Full API Documentation**: http://localhost/api/doc

## Testing

### Code Quality
- **PHPStan** for static analysis
- **PHPCS** for coding standards
- **Deptrac** for architecture enforcement
- **PHPUnit** for testing

### Run tests
```bash
./bin/tests.sh
```

## Run kubernetes infrastructure locally using minikube

    minikube start --driver=docker
    minikube addons enable storage-provisioner
