package messaging

import (
	"github.com/rabbitmq/amqp091-go"
	"go.uber.org/zap"
)

type RabbitMQEventPublisher struct {
	conn    *amqp091.Connection
	channel *amqp091.Channel
	logger  *zap.Logger
}
