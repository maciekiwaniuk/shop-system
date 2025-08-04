FROM golang:1.24-alpine

RUN go install github.com/air-verse/air@latest
RUN go install github.com/sqlc-dev/sqlc/cmd/sqlc@latest

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
