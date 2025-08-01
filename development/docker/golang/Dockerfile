FROM golang:1.24-alpine

RUN apk --no-cache add ca-certificates

WORKDIR /app

COPY go.mod go.sum ./

RUN go mod download

COPY . .

RUN go mod tidy

RUN CGO_ENABLED=0 GOOS=linux go build -ldflags='-w -s' -o main ./cmd/main.go

RUN chmod +x main

EXPOSE 8080

CMD ["./main"]