package query

import (
	"context"
	"payments/internal/domain"
)

type TransactionsByPayerId struct {
	payerId string
}

type TransactionsByPayerIdHandler struct {
	repo domain.TransactionRepository
}

func NewTransactionsByPayerIdHandler(repo domain.TransactionRepository) TransactionsByPayerIdHandler {
	return TransactionsByPayerIdHandler{repo: repo}
}

func (h TransactionsByPayerIdHandler) Handle(ctx context.Context, cmd TransactionsByPayerId) ([]domain.Transaction, error) {
	return h.repo.GetManyByPayerId(ctx, cmd.payerId)
}
