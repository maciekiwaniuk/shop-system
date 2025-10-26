package domain

type Event interface {
	AggregateId() string
}
