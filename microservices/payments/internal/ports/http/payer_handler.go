package http

import (
	"net/http"
	"payments/internal/app"
	"payments/internal/app/command"
	"payments/internal/domain"
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

type CreatePayerRequest struct {
	Id      string `json:"id" binding:"required"`
	Email   string `json:"email" binding:"required,email,min=3,max=100"`
	Name    string `json:"name" binding:"required,min=2,max=100"`
	Surname string `json:"surname" binding:"required,min=2,max=100"`
}

func (h PayerHandler) CreatePayer(c *gin.Context) {
	var req CreatePayerRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		zap.L().Error("binding create payer request failed", zap.Error(err))
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Something went wrong while binding payer request",
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
