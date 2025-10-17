package app

import "payments/internal/app/command"

type Application struct {
	Commands Commands
	Queries  Queries
}

type Commands struct {
	CreatePayer         command.CreatePayerHandler
	InitiateTransaction command.InitiateTransactionHandler
	CompleteTransaction command.CompleteTransactionHandler
	CancelTransaction   command.CancelTransactionHandler
}

type Queries struct {
}
