package domain

import "time"

type Transaction struct {
	Id          string
	PayerId     string
	Amount      float32
	Status      string
	CompletedAt *time.Time
	CreatedAt   time.Time
}
