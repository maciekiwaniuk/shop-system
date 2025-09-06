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

type HttpServer struct {
	app app.Application
}

func NewHttpServer(app app.Application) HttpServer {
	return HttpServer{app}
}

type CreatePayerRequest struct {
	Id      string `json:"id" binding:"required"`
	Email   string `json:"email" binding:"required,email,min=3,max=100"`
	Name    string `json:"name" binding:"required,min=2,max=100"`
	Surname string `json:"surname" binding:"required,min=2,max=100"`
}

func (h HttpServer) SetupRouter(port string) error {
	r := gin.Default()

	v1 := r.Group("/v1")
	v1.GET("/health", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{
			"success": true,
			"message": "Payments service is working",
		})
	})

	p := v1.Group("/payers")
	p.POST("/create", func(c *gin.Context) {
		var req CreatePayerRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			zap.L().Error("nie tak", zap.Error(err))
			c.JSON(http.StatusBadRequest, gin.H{
				"success": false,
				"message": err,
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
			c.JSON(http.StatusInternalServerError, gin.H{
				"success": false,
				"message": "Something went wrong while creating payer",
			})
			return
		}

		c.JSON(http.StatusCreated, gin.H{
			"success": true,
			"message": "Payer created successfully",
		})
	})

	err := r.Run(port)
	return err
}
