package query

import (
	"context"
	"payments/internal/domain"
)

type TransactionById struct {
	id string
}

type TransactionByIdHandler struct {
	repo domain.TransactionRepository
}

func NewTransactionByIdHandler(repo domain.TransactionRepository) TransactionByIdHandler {
	return TransactionByIdHandler{repo: repo}
}

func (h TransactionByIdHandler) Handle(ctx context.Context, cmd TransactionById) (*domain.Transaction, error) {
	return h.repo.GetOneById(ctx, cmd.id)
}
