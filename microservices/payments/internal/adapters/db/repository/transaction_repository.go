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

func (t transactionRepository) CreateTransaction(ctx context.Context, transaction *domain.Transaction) error {
	var completedAt sql.NullTime
	if transaction.CompletedAt != nil {
		completedAt = sql.NullTime{
			Time:  *transaction.CompletedAt,
			Valid: true,
		}
	}

	arg := generated.CreateTransactionParams{
		ID:          transaction.Id,
		PayerID:     transaction.PayerId,
		Amount:      fmt.Sprintf("%.2f", transaction.Amount),
		Status:      string(transaction.Status),
		CompletedAt: completedAt,
		CreatedAt:   transaction.CreatedAt,
	}
	_, err := t.q.CreateTransaction(ctx, arg)
	return err
}

func (t transactionRepository) MarkTransactionAsPaid(ctx context.Context, transactionId string) error {
	arg := generated.UpdateTransactionStatusParams{
		Status: string(domain.StatusPaid),
		CompletedAt: sql.NullTime{
			Time:  time.Now(),
			Valid: true,
		},
		ID: transactionId,
	}
	_, err := t.q.UpdateTransactionStatus(ctx, arg)
	return err
}

func (t transactionRepository) MarkTransactionAsCanceled(ctx context.Context, transactionId string) error {
	arg := generated.UpdateTransactionStatusParams{
		Status: string(domain.StatusCanceled),
		CompletedAt: sql.NullTime{
			Time:  time.Now(),
			Valid: true,
		},
		ID: transactionId,
	}
	_, err := t.q.UpdateTransactionStatus(ctx, arg)
	return err
}

func (t transactionRepository) GetTransactionById(ctx context.Context, transactionId string) (*domain.Transaction, error) {
	record, err := t.q.GetOneTransactionById(ctx, transactionId)
	if err != nil {
		return nil, err
	}

	amount, err := strconv.ParseFloat(record.Amount, 32)
	if err != nil {
		return nil, err
	}

	var completedAt *time.Time
	if record.CompletedAt.Valid {
		completedAt = &record.CompletedAt.Time
	}

	return &domain.Transaction{
		Id:          record.ID,
		PayerId:     record.PayerID,
		Amount:      float32(amount),
		Status:      domain.TransactionStatus(record.Status),
		CompletedAt: completedAt,
		CreatedAt:   record.CreatedAt,
	}, nil
}

func (t transactionRepository) GetTransactionsByPayerId(ctx context.Context, payerId string) ([]domain.Transaction, error) {
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

		var completedAt *time.Time
		if record.CompletedAt.Valid {
			completedAt = &record.CompletedAt.Time
		}

		transactions[i] = domain.Transaction{
			Id:          record.ID,
			PayerId:     record.PayerID,
			Amount:      float32(amount),
			Status:      domain.TransactionStatus(record.Status),
			CompletedAt: completedAt,
			CreatedAt:   record.CreatedAt,
		}
	}

	return transactions, nil
}

func NewTransactionRepository(db *sql.DB) domain.TransactionRepository {
	return &transactionRepository{
		q: generated.New(db),
	}
}
