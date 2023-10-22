<?php

declare(strict_types=1);

namespace App\Tests\Shared\Application\Bus\QueryBus;

use App\Shared\Application\Bus\QueryBus\QueryBus;
use App\Shared\Application\Bus\QueryBus\QueryBusInterface;
use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryInterface;
use App\Tests\AbstractUnitTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;

class QueryBusTest extends AbstractUnitTestCase
{
    protected MessageBusInterface $bus;
    protected LoggerInterface $logger;
    protected StampInterface $stamp;
    protected QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->stamp = $this->createMock(StampInterface::class);
        $this->queryBus = new QueryBus(
            $this->bus,
            $this->logger
        );
    }

    public function testSuccessfulHandle(): void
    {
        $stamp = new HandledStamp(
            result: new QueryResult(
                success: true,
                statusCode: Response::HTTP_OK
            ),
            handlerName: 'handlerName'
        );

        $envelope = new Envelope(
            message: $stamp,
            stamps: [$stamp]
        );

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        $queryResult = $this->queryBus->handle(
            new class implements QueryInterface { }
        );

        $this->assertTrue($queryResult->success);
        $this->assertEquals(Response::HTTP_OK, $queryResult->statusCode);
    }

//    public function testHandleWhenBusDispatchesMoreThanOneStamp(): void
//    {
//        $this->stamp
//            ->expects($this->once())
//            ->method('getResult')
//            ->willReturn(
//                new QueryResult(
//                    success: true,
//                    statusCode: Response::HTTP_OK
//                )
//            );
//
//        $envelope = new Envelope(
//            message: $this->stamp,
//            stamps: [$this->stamp, $this->stamp]
//        );
//
//        $this->bus
//            ->expects($this->once())
//            ->method('dispatch')
//            ->willReturn($envelope);
//
//        $queryResult = $this->queryBus->handle(
//            new class implements QueryInterface { }
//        );
//
//        $this->assertFalse($queryResult->success);
//        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $queryResult->statusCode);
//    }
//
//    public function testHandleWhenQueryResultIsNotProperType(): void
//    {
//        $this->stamp
//            ->expects($this->once())
//            ->method('getResult')
//            ->willReturn(
//                new class {
//                    public function getResult(): bool { return true; }
//                }
//            );
//
//        $envelope = new Envelope(
//            message: $this->stamp,
//            stamps: [$this->stamp]
//        );
//
//        $this->bus
//            ->expects($this->once())
//            ->method('dispatch')
//            ->willReturn($envelope);
//
//        $queryResult = $this->queryBus->handle(
//            new class implements QueryInterface { }
//        );
//
//        $this->assertFalse($queryResult->success);
//        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $queryResult->statusCode);
//    }
}