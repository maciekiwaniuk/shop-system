package validation

import (
	"fmt"
	"strings"
)

type Error struct {
	Field   string `json:"field"`
	Message string `json:"message"`
}

type Errors struct {
	Errors map[string]string `json:"errors"`
}

func (e Errors) Error() string {
	var messages []string
	for field, message := range e.Errors {
		messages = append(messages, fmt.Sprintf("%s: %s", field, message))
	}
	return strings.Join(messages, ", ")
}
