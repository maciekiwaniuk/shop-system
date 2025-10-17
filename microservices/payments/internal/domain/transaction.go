package domain

import (
	"context"
	"time"
)

type TransactionStatus string

const (
	StatusWaitingForPayment TransactionStatus = "waiting_for_payment"
	StatusPaid              TransactionStatus = "paid"
	StatusCanceled          TransactionStatus = "canceled"
)

type Transaction struct {
	Id          string
	PayerId     string
	Amount      float32
	Status      TransactionStatus
	CompletedAt *time.Time
	CreatedAt   time.Time
}

type TransactionRepository interface {
	CreateTransaction(ctx context.Context, transaction *Transaction) error
	MarkTransactionAsPaid(ctx context.Context, transactionId string) error
	MarkTransactionAsCanceled(ctx context.Context, transactionId string) error
	GetTransactionById(ctx context.Context, transactionId string) (*Transaction, error)
	GetTransactionsByPayerId(ctx context.Context, payerId string) ([]Transaction, error)
}
