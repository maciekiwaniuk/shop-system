package service

import (
	"context"
	"encoding/json"
	"fmt"
	"net/http"
	"payments/internal/ports/external"

	"go.uber.org/zap"
)

type ClientServiceImpl struct {
	httpClient *http.Client
	baseURL    string
	logger     *zap.Logger
}

func NewClient(baseURL string, logger *zap.Logger) *ClientServiceImpl {
	return &ClientServiceImpl{
		httpClient: http.DefaultClient,
		baseURL:    baseURL,
		logger:     logger,
	}
}

func (c ClientServiceImpl) GetClientDetails(ctx context.Context, clientID string) (*external.ClientDetails, error) {
	url := fmt.Sprintf("%s/api/v1/clients/details/%s", c.baseURL, clientID)

	req, err := http.NewRequestWithContext(ctx, http.MethodGet, url, nil)
	if err != nil {
		c.logger.Error("failed to create request", zap.Error(err), zap.String("client_id", clientID))
		return nil, fmt.Errorf("failed to create request: %w", err)
	}

	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")

	resp, err := c.httpClient.Do(req)
	if err != nil {
		c.logger.Error("failed to make request", zap.Error(err), zap.String("client_id", clientID))
		return nil, fmt.Errorf("failed to make request: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		c.logger.Error("unexpected status code",
			zap.Int("status_code", resp.StatusCode),
			zap.String("client_id", clientID))
		return nil, fmt.Errorf("unexpected status code: %d", resp.StatusCode)
	}

	type ClientResponse struct {
		Success bool `json:"success"`
		Data    struct {
			Email   string `json:"email"`
			Name    string `json:"name"`
			Surname string `json:"surname"`
		} `json:"data"`
	}
	var clientResponse ClientResponse
	if err := json.NewDecoder(resp.Body).Decode(&clientResponse); err != nil {
		c.logger.Error("failed to decode response", zap.Error(err), zap.String("client_id", clientID))
		return nil, fmt.Errorf("failed to decode response: %w", err)
	}
	fmt.Println(clientResponse.Data.Email, clientResponse.Data.Name)

	return &external.ClientDetails{
		ID:      clientID,
		Email:   clientResponse.Data.Email,
		Name:    clientResponse.Data.Name,
		Surname: clientResponse.Data.Surname,
	}, nil
}
