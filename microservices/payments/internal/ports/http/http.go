package http

import (
	"net/http"
	"payments/internal/app"

	"github.com/gin-gonic/gin"
)

type HttpServer struct {
	app app.Application
}

func NewHttpServer(app app.Application) HttpServer {
	return HttpServer{app}
}

type Response struct {
	Success bool              `json:"success"`
	Message string            `json:"message"`
	Errors  map[string]string `json:"errors,omitempty"`
	Data    interface{}       `json:"data,omitempty"`
}

func (h HttpServer) SetupRouter(port string) error {
	r := gin.Default()

	v1 := r.Group("/v1")
	v1.GET("/health", func(c *gin.Context) {
		c.JSON(http.StatusOK, Response{
			Success: true,
			Message: "Payments service is working",
		})
	})

	setupPayerRoutes(v1, h.app)

	err := r.Run(port)
	return err
}

func setupPayerRoutes(rg *gin.RouterGroup, app app.Application) {
	h := NewPayerHandler(app)

	p := rg.Group("/payers")
	{
		p.POST("/create", h.CreatePayer)
	}
}
