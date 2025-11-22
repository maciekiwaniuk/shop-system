package messaging

import (
	"context"
	"encoding/json"
	"fmt"
	"payments/internal/domain"
	"payments/internal/ports/outbound"
	"time"

	"github.com/rabbitmq/amqp091-go"
	"go.uber.org/zap"
)

type RabbitMQEventPublisher struct {
	conn    *amqp091.Connection
	channel *amqp091.Channel
	logger  *zap.Logger
}

func NewRabbitMQEventPublisher(amqpURL string, logger *zap.Logger) (outbound.EventPublisher, error) {
	var conn *amqp091.Connection
	var err error
	
	// Retry connection with exponential backoff
	maxRetries := 30
	baseDelay := time.Second
	
	logger.Info("attempting to connect to RabbitMQ", zap.String("url", amqpURL))
	
	for i := 0; i < maxRetries; i++ {
		conn, err = amqp091.Dial(amqpURL)
		if err == nil {
			logger.Info("successfully connected to RabbitMQ")
			break
		}
		
		// Calculate exponential backoff with max of 10 seconds
		waitTime := baseDelay * time.Duration(1<<uint(i))
		if waitTime > 10*time.Second {
			waitTime = 10 * time.Second
		}
		
		logger.Warn("failed to connect to RabbitMQ, retrying...",
			zap.Int("attempt", i+1),
			zap.Int("max_retries", maxRetries),
			zap.Duration("wait_time", waitTime),
			zap.Error(err))
		
		time.Sleep(waitTime)
	}
	
	if err != nil {
		return nil, fmt.Errorf("failed to connect to RabbitMQ after %d attempts: %w", maxRetries, err)
	}

	ch, err := conn.Channel()
	if err != nil {
		conn.Close()
		return nil, fmt.Errorf("failed to open channel: %w", err)
	}

	err = ch.ExchangeDeclare(
		"commerce.payments_events",
		"topic",
		true,
		false,
		false,
		false,
		nil,
	)
	if err != nil {
		ch.Close()
		conn.Close()
		return nil, fmt.Errorf("failed to declare exchange: %w", err)
	}

	logger.Info("RabbitMQ event publisher initialized successfully")

	return &RabbitMQEventPublisher{
		conn:    conn,
		channel: ch,
		logger:  logger,
	}, nil
}

func (p *RabbitMQEventPublisher) publishEvent(ctx context.Context, routingKey string, event domain.Event) error {
	eventMap := map[string]interface{}{
		"routing_key": routingKey,
	}

	body, err := json.Marshal(event)
	if err != nil {
		return err
	}

	var eventData map[string]interface{}
	if err := json.Unmarshal(body, &eventData); err != nil {
		return err
	}

	eventMap["transaction_id"] = eventData["transaction_id"]

	switch event.(type) {
	case domain.TransactionCompletedEvent:
		eventMap["completed_at"] = eventData["completed_at"]
	case domain.TransactionCanceledEvent:
		eventMap["canceled_at"] = eventData["canceled_at"]
	}

	finalBody, err := json.Marshal(eventMap)
	if err != nil {
		return err
	}

	ctx, cancel := context.WithTimeout(ctx, 5*time.Second)
	defer cancel()

	return p.channel.PublishWithContext(
		ctx,
		"commerce.payments_events",
		routingKey,
		false,
		false,
		amqp091.Publishing{
			ContentType:  "application/json",
			Body:         finalBody,
			Timestamp:    time.Now(),
			DeliveryMode: amqp091.Persistent,
			MessageId:    event.AggregateId(),
		},
	)
}

func (p *RabbitMQEventPublisher) TransactionCompleted(ctx context.Context, event domain.TransactionCompletedEvent) error {
	return p.publishEvent(ctx, "transaction_completed", event)
}

func (p *RabbitMQEventPublisher) TransactionCanceled(ctx context.Context, event domain.TransactionCanceledEvent) error {
	return p.publishEvent(ctx, "transaction_canceled", event)
}
