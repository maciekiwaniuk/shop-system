-- name: CreateTransaction :execresult
INSERT INTO `transaction` (
    id, payer_id, amount, completed_at, created_at
) VALUES (
    ?, ?, ?, ?, ?
);

-- name: GetOneTransactionById :one
SELECT *
FROM `transaction`
WHERE id LIKE $1;
