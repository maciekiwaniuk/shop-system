package validation

type CreatePayerRequest struct {
	ID      string `json:"id" validate:"required,min=1,max=100"`
	Email   string `json:"email" validate:"required,email,min=3,max=100"`
	Name    string `json:"name" validate:"required,min=2,max=100"`
	Surname string `json:"surname" validate:"required,min=2,max=100"`
}
