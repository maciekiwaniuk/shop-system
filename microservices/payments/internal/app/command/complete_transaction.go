package command

import (
	"context"
	"payments/internal/domain"
	"payments/internal/ports/outbound"
	"time"
)

type CompleteTransaction struct {
	Id string
}

type CompleteTransactionHandler struct {
	repo           domain.TransactionRepository
	eventPublisher outbound.EventPublisher
}

func NewCompleteTransactionHandler(repo domain.TransactionRepository, eventPublisher outbound.EventPublisher) CompleteTransactionHandler {
	return CompleteTransactionHandler{repo: repo, eventPublisher: eventPublisher}
}

func (h CompleteTransactionHandler) Handle(ctx context.Context, cmd CompleteTransaction) error {
	err := h.repo.MarkAsPaidById(ctx, cmd.Id)
	if err != nil {
		return err
	}

	event := domain.TransactionCompletedEvent{
		TransactionId: cmd.Id,
		CompletedAt:   time.Now().Format(time.RFC3339),
	}
	return h.eventPublisher.TransactionCompleted(ctx, event)
}
