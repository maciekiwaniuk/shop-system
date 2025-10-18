package domain

import (
	"context"
	"time"
)

type Payer struct {
	Id        string
	Email     string
	Name      string
	Surname   string
	UpdatedAt time.Time
	CreatedAt time.Time
}

type PayerRepository interface {
	Create(ctx context.Context, payer *Payer) error
	FindById(ctx context.Context, id string) (*Payer, error)
}
