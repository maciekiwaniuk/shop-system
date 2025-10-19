package http

import (
	"net/http"
	"payments/internal/app"
	"payments/internal/app/command"
	"payments/internal/app/query"
	"payments/internal/domain"
	"payments/internal/ports/validation"
	"time"

	"github.com/gin-gonic/gin"
	"go.uber.org/zap"
)

type TransactionHandler struct {
	app app.Application
}

func NewTransactionHandler(app app.Application) TransactionHandler {
	return TransactionHandler{app: app}
}

func (h TransactionHandler) Initiate(c *gin.Context) {
	var req validation.InitiateTransactionRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		zap.L().Error("binding initiation of transaction request failed", zap.Error(err))
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Something went wrong while binding initiation of transaction request",
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

	cmd := command.InitiateTransaction{
		Transaction: domain.Transaction{
			Id:         req.Id,
			PayerId:    req.PayerId,
			Amount:     req.Amount,
			Status:     domain.StatusWaitingForPayment,
			FinishedAt: nil,
			CreatedAt:  time.Now(),
		},
	}

	if err := h.app.Commands.InitiateTransaction.Handle(c.Request.Context(), cmd); err != nil {
		zap.L().Error("Failed to initiate transaction", zap.Error(err))
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Something went wrong while initiating transaction",
		})
		return
	}

	c.JSON(http.StatusCreated, Response{
		Success: true,
		Message: "Transaction initiated successfully",
	})
}

func (h TransactionHandler) Complete(c *gin.Context) {
	transactionId := c.Param("id")
	if transactionId == "" {
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Transaction id is required",
		})
		return
	}

	cmd := command.CompleteTransaction{
		Id: transactionId,
	}

	if err := h.app.Commands.CompleteTransaction.Handle(c.Request.Context(), cmd); err != nil {
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Something went wrong while completing transaction",
		})
		return
	}
	c.JSON(http.StatusOK, Response{
		Success: true,
		Message: "Transaction completed successfully",
	})
}

func (h TransactionHandler) Cancel(c *gin.Context) {
	transactionId := c.Param("id")
	if transactionId == "" {
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Transaction id is required",
		})
		return
	}

	cmd := command.CancelTransaction{
		Id: transactionId,
	}

	if err := h.app.Commands.CancelTransaction.Handle(c.Request.Context(), cmd); err != nil {
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Something went wrong while canceling transaction",
		})
		return
	}
	c.JSON(http.StatusOK, Response{
		Success: true,
		Message: "Transaction canceled successfully",
	})
}

func (h TransactionHandler) OneById(c *gin.Context) {
	transactionId := c.Param("id")
	if transactionId == "" {
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Transaction id is required",
		})
		return
	}

	cmd := query.TransactionById{
		Id: transactionId,
	}

	transaction, err := h.app.Queries.GetTransactionById.Handle(c.Request.Context(), cmd)
	if err != nil {
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Something went wrong while getting transaction by id",
		})
		return
	}

	c.JSON(http.StatusOK, Response{
		Success: true,
		Data:    transaction,
	})
	return
}

func (h TransactionHandler) ManyByPayerId(c *gin.Context) {
	payerId := c.Param("payer_id")
	if payerId == "" {
		c.JSON(http.StatusBadRequest, Response{
			Success: false,
			Message: "Payer id is required",
		})
		return
	}

	cmd := query.TransactionsByPayerId{
		PayerId: payerId,
	}

	transactions, err := h.app.Queries.GetTransactionsByPayerId.Handle(c.Request.Context(), cmd)
	if err != nil {
		c.JSON(http.StatusInternalServerError, Response{
			Success: false,
			Message: "Something went wrong while getting transactions by payer id",
		})
		return
	}

	c.JSON(http.StatusOK, Response{
		Success: true,
		Data:    transactions,
	})
	return
}
