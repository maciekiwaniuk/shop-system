-- name: CreatePayer :execresult
INSERT INTO payer (
    id, email, name, surname, updated_at, created_at
) VALUES (
    ?, ?, ?, ?, ?, ?
);

-- name: FindPayerById :one
SELECT *
FROM payer
WHERE id = ?;
