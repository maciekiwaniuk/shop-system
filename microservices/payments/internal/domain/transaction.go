package domain

import "time"

type Transaction struct {
	id          string
	payerId     string
	amount      float32
	status      string
	completedAt *time.Time
	createdAt   time.Time
}
