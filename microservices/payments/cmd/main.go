package main

import (
	"log"
	"os"
	"payments/internal/adapters/db"
	"payments/internal/ports/http"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"
	"go.uber.org/zap"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Println("no .env file found, using system environment variables")
	}

	if os.Getenv("GIN_MODE") == "release" {
		gin.SetMode(gin.ReleaseMode)
	}

	logger, err := zap.NewProduction()
	if err != nil {
		log.Fatalf("failed to initialize logger: %v", err)
	}
	defer logger.Sync()

	database, err := db.Connect()
	if err != nil {
		logger.Fatal("failed to connect to database", zap.Error(err))
	}
	defer database.Close()

	logger.Info("successfully connected to database")

	router := http.SetupRouter()

	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	logger.Info("starting payments service", zap.String("port", port))
	if err := router.Run(":" + port); err != nil {
		logger.Fatal("failed to start server", zap.Error(err))
	}
}
