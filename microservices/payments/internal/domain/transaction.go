package domain

import (
	"context"
	"time"
)

type Transaction struct {
	Id          string
	PayerId     string
	Amount      float32
	Status      string
	CompletedAt *time.Time
	CreatedAt   time.Time
}

type TransactionRepository interface {
	CreateTransaction(ctx context.Context, transaction *Transaction) error
	GetTransactionById(ctx context.Context, transactionId string) (*Transaction, error)
	GetTransactionsByPayerId(ctx context.Context, payerId string) ([]Transaction, error)
}
