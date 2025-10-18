package command

import (
	"context"
	"fmt"
	"payments/internal/domain"
	"payments/internal/ports/external"
	"time"
)

type InitiateTransaction struct {
	Transaction domain.Transaction
}

type InitiateTransactionHandler struct {
	transactionRepo domain.TransactionRepository
	payerRepository domain.PayerRepository
	clientService   external.ClientService
}

func NewInitiateTransactionHandler(payerRepo domain.PayerRepository, transactionRepo domain.TransactionRepository, clientService external.ClientService) InitiateTransactionHandler {
	return InitiateTransactionHandler{
		transactionRepo: transactionRepo,
		payerRepository: payerRepo,
		clientService:   clientService,
	}
}

func (h InitiateTransactionHandler) Handle(ctx context.Context, cmd InitiateTransaction) error {
	payer, err := h.payerRepository.FindById(ctx, cmd.Transaction.PayerId)
	if err != nil {
		return err
	}
	if payer == nil {
		fetchedPayer, err := h.clientService.GetClientDetails(ctx, cmd.Transaction.PayerId)
		if err != nil {
			return err
		}
		fmt.Println("TUTAJ NAME")
		fmt.Println(fetchedPayer.Name)
		if err := h.payerRepository.Create(ctx, &domain.Payer{
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
