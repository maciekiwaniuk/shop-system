package external

import "context"

type ClientDetails struct {
	ID      string `json:"id"`
	Email   string `json:"email"`
	Name    string `json:"name"`
	Surname string `json:"surname"`
}

type ClientService interface {
	GetClientDetails(ctx context.Context, ClientID string) (*ClientDetails, error)
}
