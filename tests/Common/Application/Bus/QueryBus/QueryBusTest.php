<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\Bus\QueryBus;

use App\Common\Application\Bus\QueryBus\QueryBus;
use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryInterface;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[Group('unit')]
class QueryBusTest extends AbstractUnitTestCase
{
    private MessageBusInterface $bus;
    private LoggerInterface $logger;
    private QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = new QueryBus(
            $this->bus,
            $this->logger,
        );
    }

    #[Test]
    public function will_handle(): void
    {
        $stamp = new HandledStamp(
            result: new QueryResult(
                success: true,
                statusCode: Response::HTTP_OK,
            ),
            handlerName: 'handlerName',
        );
        $envelope = new Envelope(
            message: $stamp,
            stamps: [$stamp],
        );
        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        $queryResult = $this->queryBus->handle(
            new class implements QueryInterface {
            },
        );

        $this->assertTrue($queryResult->success);
        $this->assertEquals(Response::HTTP_OK, $queryResult->statusCode);
    }

    #[Test]
    public function will_fail_when_bus_dispatches_more_than_one_stamp(): void
    {
        $stamp = new HandledStamp(
            result: new QueryResult(
                success: true,
                statusCode: Response::HTTP_OK,
            ),
            handlerName: 'exampleHandlerName',
        );
        $envelope = new Envelope(
            message: $stamp,
            stamps: [$stamp, $stamp],
        );
        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        $queryResult = $this->queryBus->handle(
            new class implements QueryInterface {
            },
        );

        $this->assertFalse($queryResult->success);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $queryResult->statusCode);
    }

    #[Test]
    public function will_fail_when_query_result_is_not_proper_type(): void
    {
        $stamp = new HandledStamp(
            result: new class {
            },
            handlerName: 'exampleHandlerName',
        );
        $envelope = new Envelope(
            message: $stamp,
            stamps: [$stamp],
        );
        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        $queryResult = $this->queryBus->handle(
            new class implements QueryInterface {
            },
        );

        $this->assertFalse($queryResult->success);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $queryResult->statusCode);
    }
}
