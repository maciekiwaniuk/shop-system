package repository

import (
	"context"
	"database/sql"
	"payments/internal/adapters/db/query/generated"
	"payments/internal/domain"
)

type payerRepository struct {
	q *generated.Queries
}

func (p payerRepository) CreatePayer(ctx context.Context, payer *domain.Payer) error {
	arg := generated.CreatePayerParams{
		ID:        payer.Id,
		Email:     payer.Email,
		Name:      payer.Name,
		Surname:   payer.Surname,
		UpdatedAt: payer.UpdatedAt,
		CreatedAt: payer.CreatedAt,
	}
	_, err := p.q.CreatePayer(ctx, arg)
	return err
}

func NewPayerRepository(db *sql.DB) domain.PayerRepository {
	return &payerRepository{
		q: generated.New(db),
	}
}
