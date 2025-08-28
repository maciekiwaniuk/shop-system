-- name: Create
INSERT INTO payment (
    id, payer_id, amount, completed_at, created_at
) VALUES (
    $1, $2, $3, $4, $5
);

-- name: GetOneById: one
SELECT *
FROM payment
WHERE id = $1;

-- name: GetPaginated: many

