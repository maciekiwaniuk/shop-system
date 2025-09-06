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

func NewCreatePayerHandler(repo domain.PayerRepository) CreatePayerHandler {
	return CreatePayerHandler{repo: repo}
}

func (h CreatePayerHandler) Handle(ctx context.Context, cmd CreatePayer) error {
	return h.repo.CreatePayer(ctx, &cmd.Payer)
}
