package payer

import "context"

type PayerRepository interface {
	Save(ctx context.Context, p *Payer)
}
