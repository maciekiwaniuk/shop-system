package messaging

import (
	"context"
	"encoding/json"
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
	conn, err := amqp091.Dial(amqpURL)
	if err != nil {
		return nil, err
	}

	ch, err := conn.Channel()
	if err != nil {
		return nil, err
	}

	err = ch.ExchangeDeclare(
		"payments_events",
		"topic",
		true,
		false,
		false,
		false,
		nil,
	)
	if err != nil {
		return nil, err
	}

	return &RabbitMQEventPublisher{
		conn:    conn,
		channel: ch,
		logger:  logger,
	}, nil
}

func (p *RabbitMQEventPublisher) publishEvent(ctx context.Context, routingKey string, event domain.Event) error {
	body, err := json.Marshal(event)
	if err != nil {
		return nil
	}

	ctx, cancel := context.WithTimeout(ctx, 5*time.Second)
	defer cancel()

	return p.channel.PublishWithContext(
		ctx,
		"payments_events",
		routingKey,
		false,
		false,
		amqp091.Publishing{
			ContentType:  "application/json",
			Body:         body,
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
