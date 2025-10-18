package validation

type CreatePayerRequest struct {
	Id      string `json:"id" validate:"required,min=1,max=100"`
	Email   string `json:"email" validate:"required,email,min=3,max=100"`
	Name    string `json:"name" validate:"required,min=2,max=100"`
	Surname string `json:"surname" validate:"required,min=2,max=100"`
}

type InitiateTransactionRequest struct {
	Id      string  `json:"id" validate:"required,min=1,max=100"`
	PayerId string  `json:"payer_id" validate:"required,min=1,max=100"`
	Amount  float32 `json:"amount" validate:"required"`
}
