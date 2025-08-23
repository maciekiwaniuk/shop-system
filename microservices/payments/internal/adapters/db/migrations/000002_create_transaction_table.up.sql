CREATE TABLE transaction (
    id VARCHAR(255) PRIMARY KEY,
    payer_id VARCHAR(255) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    completed_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL,
);
