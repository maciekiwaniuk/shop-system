package domain

import "time"

type Payer struct {
	id        string
	email     string
	name      string
	surname   string
	updatedAt time.Time
	createdAt time.Time
}
