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

	zap.ReplaceGlobals(zap.Must(zap.NewProduction()))

	a := newApplication()
	server := http.NewHttpServer(a)

	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	zap.L().Info("Starting payments service", zap.String("port", port))
	if err := server.SetupRouter(":" + port); err != nil {
		zap.L().Error("Failed to setup router", zap.Error(err))
	}
}

func newApplication() app.Application {
	dbConn, err := db.Connect()
	if err != nil {
		panic(err)
	}

	payerRepo := adapters.NewPayerRepository(dbConn)
	transactionRepo := adapters.NewTransactionRepository(dbConn)

	return app.Application{
		Commands: app.Commands{
			CreatePayer:         command.NewCreatePayerHandler(payerRepo),
			InitiateTransaction: command.NewInitiateTransactionHandler(transactionRepo),
			CompleteTransaction: command.NewCompleteTransactionHandler(transactionRepo),
			CancelTransaction:   command.NewCancelTransactionHandler(transactionRepo),
		},
		Queries: app.Queries{},
	}
}
