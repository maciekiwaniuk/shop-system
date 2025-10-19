package external

import "context"

type ClientDetails struct {
	ID      string `json:"id"`
	Email   string `json:"email"`
	Name    string `json:"name"`
	Surname string `json:"surname"`
}

type ClientService interface {
	GetDetails(ctx context.Context, clientID string) (*ClientDetails, error)
}
