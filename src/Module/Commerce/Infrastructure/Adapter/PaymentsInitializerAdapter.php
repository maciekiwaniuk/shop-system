<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Adapter;

use App\Module\Commerce\Application\Port\PaymentsInitializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class PaymentsInitializerAdapter implements PaymentsInitializerInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $paymentsServiceUrl,
    ) {
    }

    public function init(string $orderId, string $userId, float $totalCost): bool
    {
        try {
            $response = $this->httpClient->request('POST', $this->paymentsServiceUrl . '/v1/transactions/initiate', [
                'json' => [
                    'id' => $orderId,
                    'payer_id' => $userId,
                    'amount' => $totalCost,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            return $response->getStatusCode() === Response::HTTP_CREATED;
        } catch (Throwable $exception) {
            $this->logger->error('Failed to initialize payment', [
                'id' => $orderId,
                'payer_id' => $userId,
                'amount' => $totalCost,
                'error' => $exception->getMessage(),
            ]);
            return false;
        }
    }
}
