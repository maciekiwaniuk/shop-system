package command

import (
	"context"
	"payments/internal/domain"
	"payments/internal/ports/external"
	"time"
)

type InitiateTransaction struct {
	Transaction domain.Transaction
}

type InitiateTransactionHandler struct {
	transactionRepo domain.TransactionRepository
	payerRepo       domain.PayerRepository
	clientService   external.ClientService
}

func NewInitiateTransactionHandler(payerRepo domain.PayerRepository, transactionRepo domain.TransactionRepository, clientService external.ClientService) InitiateTransactionHandler {
	return InitiateTransactionHandler{
		transactionRepo: transactionRepo,
		payerRepo:       payerRepo,
		clientService:   clientService,
	}
}

func (h InitiateTransactionHandler) Handle(ctx context.Context, cmd InitiateTransaction) error {
	payer, err := h.payerRepo.FindById(ctx, cmd.Transaction.PayerId)
	if err != nil {
		return err
	}
	if payer == nil {
		fetchedPayer, err := h.clientService.GetDetails(ctx, cmd.Transaction.PayerId)
		if err != nil {
			return err
		}
		if err := h.payerRepo.Create(ctx, &domain.Payer{
			Id:        fetchedPayer.ID,
			Email:     fetchedPayer.Email,
			Name:      fetchedPayer.Name,
			Surname:   fetchedPayer.Surname,
			UpdatedAt: time.Now(),
			CreatedAt: time.Now(),
		}); err != nil {
			return err
		}
	}

	return h.transactionRepo.Create(ctx, &cmd.Transaction)
}
