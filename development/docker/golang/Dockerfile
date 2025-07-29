FROM golang:1.24-alpine AS builder

WORKDIR /app

# Copy go mod files first for better caching
COPY go.mod go.sum ./

# Download dependencies
RUN go mod download

# Copy the rest of the application
COPY . .

# Build the application
RUN CGO_ENABLED=0 GOOS=linux go build -ldflags='-w -s' -o main ./cmd/main.go

# Final stage
FROM alpine:3.20

# Install ca-certificates for HTTPS requests
RUN apk --no-cache add ca-certificates

WORKDIR /app

# Copy the binary from builder stage
COPY --from=builder /app/main .

# Make it executable
RUN chmod +x main

# Expose port (adjust if needed for your Gin app)
EXPOSE 8080

# Run the application
CMD ["./main"]
