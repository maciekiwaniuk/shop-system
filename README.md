# Shop System

A **simple e-commerce system** built with modern, popular technologies, showcasing advanced software architecture patterns and best practices.

#### This project is kind of sandbox for me for experimenting and learning new techniques and technologies. It’s more meant to be an ongoing project rather than something finished once and for all. I also see it as a great way to showcase my work and coding skills. Not everything here is optimized for maximum efficiency and how I should approach to these problems in real commercial projects - some parts, such as the communication between the payment microservice and the monolith, are intentionally implemented using mixed - sync and async communication because I wanted to test pros and cons and that's for me best way of learning something in efficient way.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PHPUnit](https://img.shields.io/badge/PHPUnit-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Kubernetes](https://img.shields.io/badge/Kubernetes-326CE5?style=for-the-badge&logo=kubernetes&logoColor=white)
![Microservices](https://img.shields.io/badge/Microservices-2C3E50?style=for-the-badge&logo=kubernetes&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Elasticsearch](https://img.shields.io/badge/Elasticsearch-005571?style=for-the-badge&logo=elasticsearch&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white)
![RabbitMQ](https://img.shields.io/badge/RabbitMQ-FF6600?style=for-the-badge&logo=rabbitmq&logoColor=white)
![MailHog](https://img.shields.io/badge/MailHog-FF6B6B?style=for-the-badge&logo=gmail&logoColor=white)![TypeScript](https://img.shields.io/badge/TypeScript-3178C6?style=for-the-badge&logo=typescript&logoColor=white)
![React](https://img.shields.io/badge/React-61DAFB?style=for-the-badge&logo=react&logoColor=black)
![Next.js](https://img.shields.io/badge/Next.js-000000?style=for-the-badge&logo=nextdotjs&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Golang](https://img.shields.io/badge/Go-00ADD8?style=for-the-badge&logo=go&logoColor=white)
![Gin](https://img.shields.io/badge/Gin-00ADD8?style=for-the-badge&logo=go&logoColor=white)

## General description

At the beginning, when I started this project to learn Symfony, I made it as a simple monolith. Later, I decided to learn about the modular monolith, and then the hexagonal architecture. That’s how this project has been evolving alongside my experience and curiosity about learning new approaches and technologies.
The main application is built in PHP with Symfony as a modular monolith, where one module is a simple authentication service and the other, commerce, is a bit more complex. The payments microservice is written in Go using the Gin framework. Both the monolith and the payments microservice are made using ports and adapters architecture and also CQRS.

Each module or microservice has it own context -> for example same entity representation in auth service will be named user - in clients module it is client - payments service it is payer, records are replicated through whole system. Each module or service has own separate database. 
Replication of data is only done when it's necessary -> for example when someone tries to make purchase without account (unique id is associated to email) then records is only being created in payments and clients modules. Only when user decide to make account then record with given id and email is being created in user module.
Modules in monolith have also separated databases so you can say easily this is configuration is ready for separating monolith to microservices. With common database it would be much harder and in my opinion microservices with common databases are not as powerful as with separated.

### CQRS Implementation
- **Queries** handle read operations (search products, get order details)
- **Sync Commands** handle write operations that needs to be done instantly (create product, update order status)
- **Async Commands** for operations that can be delayed or might take some time to complete (email notifications)

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
POST /api/v1/register
POST /api/v1/login
```

### Products
```http
GET    /api/v1/products/get-paginated 
POST   /api/v1/products/create 
GET    /api/v1/products/show/{slug}
PUT    /api/v1/products/update/{id}
DELETE /api/v1/products/delete/{id}
GET    /api/v1/products/search
```

### Orders
```http
GET  /api/v1/orders/get-paginated
POST /api/v1/orders/create
GET  /api/v1/orders/show/{uuid}
POST /api/v1/orders/change-status/{uuid}
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
