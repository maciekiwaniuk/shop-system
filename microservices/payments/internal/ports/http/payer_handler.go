package http

import (
	"net/http"
	"payments/internal/app"
	"payments/internal/app/command"
	"payments/internal/domain"
	"payments/internal/ports/validation"
	"time"

	"github.com/gin-gonic/gin"
	"go.uber.org/zap"
)

type PayerHandler struct {
	app app.Application
}

func NewPayerHandler(app app.Application) PayerHandler {
	return PayerHandler{app: app}
}

func (h PayerHandler) CreatePayer(c *gin.Context) {
	var req validation.CreatePayerRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		zap.L().Error("binding create payer request failed", zap.Error(err))
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Something went wrong while binding payer request",
		})
		return
	}

	validationErrors, err := validation.ValidateStruct(req)
	if err != nil {
		zap.L().Error("validation system error", zap.Error(err))
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Validation system error",
		})
		return
	}

	if len(validationErrors) > 0 {
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Errors:  validationErrors,
		})
		return
	}

	cmd := command.CreatePayer{
		Payer: domain.Payer{
			Id:        req.Id,
			Email:     req.Email,
			Name:      req.Name,
			Surname:   req.Surname,
			CreatedAt: time.Now(),
			UpdatedAt: time.Now(),
		},
	}

	if err := h.app.Commands.CreatePayer.Handle(c.Request.Context(), cmd); err != nil {
		zap.L().Error("Failed to create payer", zap.Error(err))
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Something went wrong while creating payer",
		})
		return
	}

	c.JSON(http.StatusCreated, Response{
		Success: true,
		Message: "Payer created successfully",
	})
}
