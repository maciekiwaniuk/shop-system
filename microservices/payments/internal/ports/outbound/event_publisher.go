package outbound

import (
	"context"
	"payments/internal/domain"
)

type EventPublisher interface {
	TransactionCompleted(ctx context.Context, event domain.TransactionCompletedEvent) error
	TransactionCanceled(ctx context.Context, event domain.TransactionCanceledEvent) error
}
