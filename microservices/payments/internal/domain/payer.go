package domain

import "time"

type Payer struct {
	Id        string
	Email     string
	Name      string
	Surname   string
	UpdatedAt time.Time
	CreatedAt time.Time
}
