# Payments Microservice

A Go microservice for handling payment processing using Gin framework.

## Features

- RESTful API with Gin framework
- Health check endpoint
- Environment configuration
- Structured logging with Logrus
- Docker support

## Quick Start

### Local Development

1. **Navigate to the payments directory:**
   ```bash
   cd microservices/payments
   ```

2. **Install dependencies:**
   ```bash
   go mod tidy
   ```

3. **Copy environment file:**
   ```bash
   cp env.example .env
   ```

4. **Run the service:**
   ```bash
   go run cmd/main.go
   ```

5. **Test the service:**
   ```bash
   curl http://localhost:8080/health
   curl http://localhost:8080/api/v1/payments
   ```

### Docker Development

1. **Build and run with Docker Compose:**
   ```bash
   cd development
   docker-compose up shop-system-payments
   ```

2. **Or build manually:**
   ```bash
   docker build -f development/docker/golang/Dockerfile -t payments-service .
   docker run -p 8080:8080 payments-service
   ```

## API Endpoints

- `GET /health` - Health check
- `GET /api/v1/payments` - Basic payments endpoint

## Environment Variables

- `PORT` - Server port (default: 8080)
- `GIN_MODE` - Gin mode (debug/release)
- `LOG_LEVEL` - Logging level

## Project Structure

```
microservices/payments/
├── cmd/
│   └── main.go          # Application entry point
├── internal/             # Private application code
├── pkg/                  # Public libraries
├── go.mod               # Go module file
├── go.sum               # Go module checksums
├── env.example          # Environment variables example
└── README.md            # This file
```

## Next Steps

- Add database integration (PostgreSQL)
- Add Redis for caching
- Implement payment processing logic
- Add authentication middleware
- Add comprehensive tests
- Add API documentation with Swagger 