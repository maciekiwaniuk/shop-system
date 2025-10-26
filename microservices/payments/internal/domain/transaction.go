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
	Id         string            `json:"id"`
	PayerId    string            `json:"payer_id"`
	Amount     float32           `json:"amount"`
	Status     TransactionStatus `json:"status"`
	FinishedAt *time.Time        `json:"finished_at"`
	CreatedAt  time.Time         `json:"created_at"`
}

type TransactionRepository interface {
	Create(ctx context.Context, transaction *Transaction) error
	MarkAsPaidById(ctx context.Context, id string) error
	MarkAsCanceledById(ctx context.Context, id string) error
	GetOneById(ctx context.Context, id string) (*Transaction, error)
	GetManyByPayerId(ctx context.Context, payerId string) ([]Transaction, error)
}

type TransactionCompletedEvent struct {
	TransactionId string `json:"transaction_id"`
	CompletedAt   string `json:"completed_at"`
}

func (t TransactionCompletedEvent) AggregateId() string {
	return t.TransactionId
}

type TransactionCanceledEvent struct {
	TransactionId string `json:"transaction_id"`
	CanceledAt    string `json:"canceled_at"`
}

func (t TransactionCanceledEvent) AggregateId() string {
	return t.TransactionId
}
