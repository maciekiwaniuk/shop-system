package common

type DomainError struct {
	message string
	code    int
}

func (e DomainError) Error() string {
	return e.message
}

func NewDomainError(message string, code int) DomainError {
	return DomainError{message: message, code: code}
}
