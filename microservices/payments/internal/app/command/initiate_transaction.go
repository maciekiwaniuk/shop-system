package command

import (
	"context"
	"payments/internal/domain"
)

type InitiateTransaction struct {
	Transaction domain.Transaction
}

type InitiateTransactionHandler struct {
	repo domain.TransactionRepository
}

func NewInitiateTransactionHandler(repo domain.TransactionRepository) InitiateTransactionHandler {
	return InitiateTransactionHandler{repo: repo}
}

func (h InitiateTransactionHandler) Handle(ctx context.Context, cmd InitiateTransaction) error {
	return h.repo.CreateTransaction(ctx, &cmd.Transaction)
}
