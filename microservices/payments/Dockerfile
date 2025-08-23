FROM golang:1.24-alpine

# hot reload
RUN go install github.com/air-verse/air@latest

# sql to golang tool
RUN go install github.com/sqlc-dev/sqlc/cmd/sqlc@latest

# golang-migrate for database migrations
RUN go install -tags 'mysql' github.com/golang-migrate/migrate/v4/cmd/migrate@latest

RUN apk --no-cache add ca-certificates git

WORKDIR /app

COPY go.mod go.sum ./

RUN go mod download
RUN go mod verify
RUN go mod tidy

COPY .air.toml ./

COPY . .

RUN mkdir -p tmp

RUN go mod download

EXPOSE 8080

CMD ["air", "-c", ".air.toml"]
