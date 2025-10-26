package command

import (
	"context"
	"payments/internal/domain"
	"payments/internal/ports/outbound"
	"time"
)

type CancelTransaction struct {
	Id string
}

type CancelTransactionHandler struct {
	repo           domain.TransactionRepository
	eventPublisher outbound.EventPublisher
}

func NewCancelTransactionHandler(repo domain.TransactionRepository, eventPublisher outbound.EventPublisher) CancelTransactionHandler {
	return CancelTransactionHandler{repo: repo, eventPublisher: eventPublisher}
}

func (h CancelTransactionHandler) Handle(ctx context.Context, cmd CancelTransaction) error {
	err := h.repo.MarkAsCanceledById(ctx, cmd.Id)
	if err != nil {
		return err
	}

	event := domain.TransactionCanceledEvent{
		TransactionId: cmd.Id,
		CanceledAt:    time.Now().Format(time.RFC3339),
	}
	return h.eventPublisher.TransactionCanceled(ctx, event)
}
