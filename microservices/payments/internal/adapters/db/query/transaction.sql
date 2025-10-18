-- name: CreateTransaction :execresult
INSERT INTO `transaction` (
    id, payer_id, status, amount, finished_at, created_at
) VALUES (
    ?, ?, ?, ?, ?, ?
);

-- name: UpdateTransactionStatus :execresult
UPDATE `transaction`
SET status = ?, finished_at = ?
WHERE id = ?;

-- name: GetOneTransactionById :one
SELECT *
FROM `transaction`
WHERE id LIKE ?;

-- name: GetManyTransactionsByPayerId :many
SELECT *
FROM `transaction`
WHERE payer_id LIKE ?;
