package command

import (
	"context"
	"payments/internal/domain"
)

type CancelTransaction struct {
	Transaction domain.Transaction
}

type CancelTransactionHandler struct {
	repo domain.TransactionRepository
}

func NewCancelTransactionHandler(repo domain.TransactionRepository) CancelTransactionHandler {
	return CancelTransactionHandler{repo: repo}
}

func (h CancelTransactionHandler) Handle(ctx context.Context, cmd CancelTransaction) error {
	return h.repo.MarkAsCanceledById(ctx, cmd.Transaction.Id)
}
