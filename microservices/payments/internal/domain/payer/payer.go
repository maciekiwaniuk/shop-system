package payer

import "time"

type Payer struct {
	ID        string
	Email     string
	Name      string
	Surname   string
	UpdatedAt time.Time
	CreatedAt time.Time
}
