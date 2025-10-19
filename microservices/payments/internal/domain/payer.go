package domain

import (
	"context"
	"time"
)

type Payer struct {
	Id        string    `json:"id""`
	Email     string    `json:"email"`
	Name      string    `json:"name"`
	Surname   string    `json:"surname"`
	UpdatedAt time.Time `json:"updated_at"`
	CreatedAt time.Time `json:"created_at"`
}

type PayerRepository interface {
	Create(ctx context.Context, payer *Payer) error
	FindById(ctx context.Context, id string) (*Payer, error)
}
