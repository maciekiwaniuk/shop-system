package command

import (
	"context"
	"payments/internal/domain"
)

type CompleteTransaction struct {
	Transaction domain.Transaction
}

type CompleteTransactionHandler struct {
	repo domain.TransactionRepository
}

func NewCompleteTransactionHandler(repo domain.TransactionRepository) CompleteTransactionHandler {
	return CompleteTransactionHandler{repo: repo}
}

func (h CompleteTransactionHandler) Handle(ctx context.Context, cmd CompleteTransaction) error {
	return h.repo.MarkTransactionAsPaid(ctx, cmd.Transaction.Id)
}
