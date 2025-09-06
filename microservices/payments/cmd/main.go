package main

import (
	"log"
	"os"
	"payments/internal/adapters/db"
	"payments/internal/adapters/db/repository"
	"payments/internal/app"
	"payments/internal/app/command"
	"payments/internal/ports/http"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"
	"go.uber.org/zap"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found, using system environment variables")
	}

	if os.Getenv("GIN_MODE") == "release" {
		gin.SetMode(gin.ReleaseMode)
	}

	logger, err := zap.NewProduction()
	if err != nil {
		log.Fatalf("Failed to initialize logger: %v", err)
	}
	defer logger.Sync()

	a := newApplication()
	server := http.NewHttpServer(a)

	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	logger.Info("Starting payments service", zap.String("port", port))
	if err := server.SetupRouter(":" + port); err != nil {
		logger.Error("Failed to setup router", zap.Error(err))
	}
}

func newApplication() app.Application {
	dbConn, err := db.Connect()
	if err != nil {
		panic(err)
	}

	payerRepository := repository.NewPayerRepository(dbConn)

	return app.Application{
		Commands: app.Commands{
			CreatePayer: command.NewCreatePayerHandler(payerRepository),
		},
		Queries: app.Queries{},
	}
}
