package domain

import "context"

type PayerRepository interface {
	CreatePayer(ctx context.Context, payer *Payer) error
}
