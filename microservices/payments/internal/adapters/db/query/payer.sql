-- name: CreatePayer :execresult
INSERT INTO payer (
    id, email, name, surname, updated_at, created_at
) VALUES (
    ?, ?, ?, ?, ?, ?
);

-- name: GetOnePayerById :one
SELECT *
FROM payer
WHERE id = $1;
