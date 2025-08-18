<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Infrastructure\Adapter;

use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Auth\Application\Port\ClientFinderInterface;
use App\Module\Commerce\Application\Query\FindClientByEmail\FindClientByEmailQuery;
use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Infrastructure\Adapter\AuthClientFinderAdapter;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

#[Group('unit')]
class AuthClientFinderAdapterTest extends AbstractUnitTestCase
{
    private QueryBusInterface $queryBus;
    private JsonSerializerInterface $serializer;
    private AuthClientFinderAdapter $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryBus = $this->createMock(QueryBusInterface::class);
        $this->serializer = $this->createMock(JsonSerializerInterface::class);
        $this->adapter = new AuthClientFinderAdapter($this->queryBus, $this->serializer);
    }

    #[Test]
    public function it_should_return_client_id_when_client_exists(): void
    {
        $email = 'test@example.com';
        $clientId = 'client-uuid-123';
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn($clientId);
        $clientData = [
            'id' => $clientId,
            'email' => $email,
            'name' => 'John',
            'surname' => 'Doe',
        ];
        $queryResult = new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: $clientData,
        );
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (FindClientByEmailQuery $query) use ($email) {
                return $query->email === $email;
            }))
            ->willReturn($queryResult);
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with(
                $this->equalTo(json_encode($clientData)),
                $this->equalTo(Client::class),
            )
            ->willReturn($client);

        $result = $this->adapter->findClientIdByEmail($email);

        $this->assertEquals($clientId, $result);
    }

    #[Test]
    public function it_should_return_null_when_client_not_found(): void
    {
        $email = 'nonexistent@example.com';
        $queryResult = new QueryResult(
            success: false,
            statusCode: Response::HTTP_NOT_FOUND,
        );
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (FindClientByEmailQuery $query) use ($email) {
                return $query->email === $email;
            }))
            ->willReturn($queryResult);
        $this->serializer
            ->expects($this->never())
            ->method('deserialize');

        $result = $this->adapter->findClientIdByEmail($email);

        $this->assertNull($result);
    }

    #[Test]
    public function it_should_return_null_when_query_returns_error_status(): void
    {
        $email = 'test@example.com';
        $queryResult = new QueryResult(
            success: false,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
        );
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->willReturn($queryResult);
        $this->serializer
            ->expects($this->never())
            ->method('deserialize');

        $result = $this->adapter->findClientIdByEmail($email);

        $this->assertNull($result);
    }

    #[Test]
    public function it_should_return_null_when_query_returns_unauthorized_status(): void
    {
        $email = 'test@example.com';
        $queryResult = new QueryResult(
            success: false,
            statusCode: Response::HTTP_UNAUTHORIZED,
        );
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->willReturn($queryResult);
        $this->serializer
            ->expects($this->never())
            ->method('deserialize');

        $result = $this->adapter->findClientIdByEmail($email);

        $this->assertNull($result);
    }

    #[Test]
    public function it_should_implement_client_finder_interface(): void
    {
        $this->assertInstanceOf(ClientFinderInterface::class, $this->adapter);
    }
}
