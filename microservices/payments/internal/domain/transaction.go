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
	Id         string
	PayerId    string
	Amount     float32
	Status     TransactionStatus
	FinishedAt *time.Time
	CreatedAt  time.Time
}

type TransactionRepository interface {
	Create(ctx context.Context, transaction *Transaction) error
	MarkAsPaidById(ctx context.Context, id string) error
	MarkAsCanceledById(ctx context.Context, id string) error
	GetOneById(ctx context.Context, id string) (*Transaction, error)
	GetManyByPayerId(ctx context.Context, payerId string) ([]Transaction, error)
}
