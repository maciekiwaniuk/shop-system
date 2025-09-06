package http

import (
	"net/http"
	"payments/internal/app"
	"payments/internal/app/command"
	"payments/internal/domain"
	"time"

	"github.com/gin-gonic/gin"
)

type HttpServer struct {
	app app.Application
}

func NewHttpServer(app app.Application) HttpServer {
	return HttpServer{app}
}

type IncomingPayer struct {
	Id      string `json:"id"`
	Email   string `json:"email"`
	Name    string `json:"name"`
	Surname string `json:"surname"`
}

func (h HttpServer) SetupRouter(port string) error {
	r := gin.Default()

	v1 := r.Group("/v1")
	v1.GET("/health", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"success": true,
			"message": "Payments service is working",
		})
	})

	p := v1.Group("/payers")
	p.POST("/create", func(c *gin.Context) {
		var payer IncomingPayer
		if err := c.ShouldBind(&payer); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{
				"success": false,
				"message": "Invalid request",
			})
		}

		cmd := command.CreatePayer{
			Payer: domain.Payer{
				Id:        payer.Id,
				Email:     payer.Email,
				Name:      payer.Name,
				Surname:   payer.Surname,
				CreatedAt: time.Now(),
				UpdatedAt: time.Now(),
			},
		}

		if err := h.app.Commands.CreatePayer.Handle(c.Request.Context(), cmd); err != nil {
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
