package adapters

import (
	"context"
	"database/sql"
	"payments/internal/adapters/db/query/generated"
	"payments/internal/domain"
)

type payerRepository struct {
	q *generated.Queries
}

func NewPayerRepository(db *sql.DB) domain.PayerRepository {
	return &payerRepository{
		q: generated.New(db),
	}
}

func (p payerRepository) Create(ctx context.Context, payer *domain.Payer) error {
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

func (p payerRepository) FindById(ctx context.Context, id string) (*domain.Payer, error) {
	payer, err := p.q.FindPayerById(ctx, id)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}
		return nil, err
	}
	return &domain.Payer{
		Id:        payer.ID,
		Email:     payer.Email,
		Name:      payer.Name,
		Surname:   payer.Surname,
		UpdatedAt: payer.UpdatedAt,
		CreatedAt: payer.CreatedAt,
	}, nil
}
