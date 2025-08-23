package domain

type PayerRepository interface {
	Save(payer *Payer) error
}
