package validation

import (
	"fmt"
	"reflect"
	"strings"

	"github.com/go-playground/validator/v10"
)

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

var validate *validator.Validate

func init() {
	validate = validator.New()

	validate.RegisterTagNameFunc(func(fld reflect.StructField) string {
		name := strings.SplitN(fld.Tag.Get("json"), ",", 2)[0]
		if name == "-" {
			return ""
		}

		words := strings.Split(name, "_")
		for i, word := range words {
			if len(word) > 0 {
				if i == 0 {
					words[i] = strings.ToUpper(string(word[0])) + strings.ToLower(word[1:])
				} else {
					words[i] = strings.ToLower(word)
				}
			}
		}
		return strings.Join(words, " ")
	})
}

func ValidateStruct(s interface{}) (map[string]string, error) {
	err := validate.Struct(s)
	if err == nil {
		return map[string]string{}, nil
	}

	validationErrors := make(map[string]string)
	for _, err := range err.(validator.ValidationErrors) {
		field := err.StructField()
		message := getCustomMessage(err)
		validationErrors[field] = message
	}
	return validationErrors, nil
}

func getCustomMessage(fe validator.FieldError) string {
	field := fe.Field()

	switch fe.Tag() {
	case "required":
		return fmt.Sprintf("%s field is required", field)
	case "min":
		return fmt.Sprintf("%s must be at least %s characters long", field, fe.Param())
	case "max":
		return fmt.Sprintf("%s must be at most %s characters long", field, fe.Param())
	case "email":
		return fmt.Sprintf("%s must be a valid email address", field)
	case "len":
		return fmt.Sprintf("%s must be exactly %s characters long", field, fe.Param())
	case "numeric":
		return fmt.Sprintf("%s must be a valid number", field)
	case "alpha":
		return fmt.Sprintf("%s must contain only letters", field)
	case "alphanum":
		return fmt.Sprintf("%s must contain only letters and numbers", field)
	case "url":
		return fmt.Sprintf("%s must be a valid URL", field)
	case "uuid":
		return fmt.Sprintf("%s must be a valid UUID", field)
	case "oneof":
		return fmt.Sprintf("%s must be one of: %s", field, fe.Param())
	case "gte":
		return fmt.Sprintf("%s must be greater than or equal to %s", field, fe.Param())
	case "lte":
		return fmt.Sprintf("%s must be less than or equal to %s", field, fe.Param())
	case "gt":
		return fmt.Sprintf("%s must be greater than %s", field, fe.Param())
	case "lt":
		return fmt.Sprintf("%s must be less than %s", field, fe.Param())
	default:
		return fmt.Sprintf("%s is not valid", field)
	}
}
