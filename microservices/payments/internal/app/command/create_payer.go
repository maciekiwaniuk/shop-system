package command

import (
	"context"
	"payments/internal/domain"
)

type CreatePayer struct {
	Payer domain.Payer
}

type CreatePayerHandler struct {
	repo domain.PayerRepository
}

func (h CreatePayerHandler) Handle(ctx context.Context, cmd CreatePayer) (err error) {
	return h.repo.CreatePayer(ctx, &cmd.Payer)
}
