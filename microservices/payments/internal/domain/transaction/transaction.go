package transaction

import (
	"time"
)

type Transaction struct {
	ID          string
	PayerID     string
	Amount      float64
	Status      string
	CompletedAt *time.Time
	CreatedAt   time.Time
}

func (t *Transaction) Complete() error {
	if t.CompletedAt != nil {
		now := time.Now()
		t.CompletedAt = &now
	}
}
