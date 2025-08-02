package http

import (
	"github.com/gin-gonic/gin"
	"net/http"
)

func SetupRouter() *gin.Engine {
	r := gin.Default()

	r.Use(gin.Logger())
	r.Use(gin.Recovery())

	r.GET("/health", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{
			"success": "true",
			"message": "Payments service is running",
		})
	})

	api := r.Group("/api/v1")
	{
		api.GET("/payments", func(c *gin.Context) {
			c.JSON(http.StatusOK, gin.H{
				"success": "true",
				"message": "Payments service is running",
			})
		})
	}
	return r
}
