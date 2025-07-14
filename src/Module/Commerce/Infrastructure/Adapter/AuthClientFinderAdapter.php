<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Adapter;

use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Auth\Application\Port\ClientFinderInterface;
use App\Module\Commerce\Application\Query\FindClientByEmail\FindClientByEmailQuery;
use App\Module\Commerce\Domain\Entity\Client;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthClientFinderAdapter implements ClientFinderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private JsonSerializerInterface $serializer,
    ) {
    }

    public function findClientIdByEmail(string $email): ?string
    {
        $queryResult = $this->queryBus->handle(new FindClientByEmailQuery($email));
        if ($queryResult->statusCode === Response::HTTP_OK) {
            /** @var Client $client */
            $client = $this->serializer->deserialize(json_encode($queryResult->data), Client::class);
            return $client->getId();
        }
        return null;
    }
}
