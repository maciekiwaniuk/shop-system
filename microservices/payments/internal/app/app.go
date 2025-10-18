package app

import (
	"payments/internal/app/command"
	"payments/internal/app/query"
)

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
	GetTransactionById       query.TransactionByIdHandler
	GetTransactionsByPayerId query.TransactionsByPayerIdHandler
}
