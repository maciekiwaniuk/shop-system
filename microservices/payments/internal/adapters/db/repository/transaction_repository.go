package adapters

import (
	"context"
	"database/sql"
	"fmt"
	"payments/internal/adapters/db/query/generated"
	"payments/internal/domain"
	"strconv"
	"time"
)

type transactionRepository struct {
	q *generated.Queries
}

func (t transactionRepository) Create(ctx context.Context, transaction *domain.Transaction) error {
	var finishedAt sql.NullTime
	if transaction.FinishedAt != nil {
		finishedAt = sql.NullTime{
			Time:  *transaction.FinishedAt,
			Valid: true,
		}
	}

	arg := generated.CreateTransactionParams{
		ID:         transaction.Id,
		PayerID:    transaction.PayerId,
		Amount:     fmt.Sprintf("%.2f", transaction.Amount),
		Status:     string(transaction.Status),
		FinishedAt: finishedAt,
		CreatedAt:  transaction.CreatedAt,
	}
	_, err := t.q.CreateTransaction(ctx, arg)
	return err
}

func (t transactionRepository) MarkAsPaidById(ctx context.Context, id string) error {
	arg := generated.UpdateTransactionStatusParams{
		Status: string(domain.StatusPaid),
		FinishedAt: sql.NullTime{
			Time:  time.Now(),
			Valid: true,
		},
		ID: id,
	}
	_, err := t.q.UpdateTransactionStatus(ctx, arg)
	return err
}

func (t transactionRepository) MarkAsCanceledById(ctx context.Context, id string) error {
	arg := generated.UpdateTransactionStatusParams{
		Status: string(domain.StatusCanceled),
		FinishedAt: sql.NullTime{
			Time:  time.Now(),
			Valid: true,
		},
		ID: id,
	}
	_, err := t.q.UpdateTransactionStatus(ctx, arg)
	return err
}

func (t transactionRepository) GetOneById(ctx context.Context, id string) (*domain.Transaction, error) {
	record, err := t.q.GetOneTransactionById(ctx, id)
	if err != nil {
		return nil, err
	}

	amount, err := strconv.ParseFloat(record.Amount, 32)
	if err != nil {
		return nil, err
	}

	var finishedAt *time.Time
	if record.FinishedAt.Valid {
		finishedAt = &record.FinishedAt.Time
	}

	return &domain.Transaction{
		Id:         record.ID,
		PayerId:    record.PayerID,
		Amount:     float32(amount),
		Status:     domain.TransactionStatus(record.Status),
		FinishedAt: finishedAt,
		CreatedAt:  record.CreatedAt,
	}, nil
}

func (t transactionRepository) GetManyByPayerId(ctx context.Context, payerId string) ([]domain.Transaction, error) {
	records, err := t.q.GetManyTransactionsByPayerId(ctx, payerId)
	if err != nil {
		return nil, err
	}

	transactions := make([]domain.Transaction, len(records))
	for i, record := range records {
		amount, err := strconv.ParseFloat(record.Amount, 32)
		if err != nil {
			return nil, err
		}

		var finishedAt *time.Time
		if record.FinishedAt.Valid {
			finishedAt = &record.FinishedAt.Time
		}

		transactions[i] = domain.Transaction{
			Id:         record.ID,
			PayerId:    record.PayerID,
			Amount:     float32(amount),
			Status:     domain.TransactionStatus(record.Status),
			FinishedAt: finishedAt,
			CreatedAt:  record.CreatedAt,
		}
	}

	return transactions, nil
}

func NewTransactionRepository(db *sql.DB) domain.TransactionRepository {
	return &transactionRepository{
		q: generated.New(db),
	}
}
