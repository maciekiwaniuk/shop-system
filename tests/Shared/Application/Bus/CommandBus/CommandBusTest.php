<?php

declare(strict_types=1);

namespace App\Tests\Shared\Application\Bus\CommandBus;

use App\Shared\Application\Bus\CommandBus\CommandBus;
use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandInterface;
use App\Tests\AbstractUnitTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBusTest extends AbstractUnitTestCase
{
    protected MessageBusInterface $bus;
    protected LoggerInterface $logger;
    protected CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->commandBus = new CommandBus(
            $this->bus,
            $this->logger
        );
    }

    public function testSuccessfulHandle(): void
    {
        $stamp = new HandledStamp(
            result: new CommandResult(
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

        $commandResult = $this->commandBus->handle(
            new class implements CommandInterface {
            }
        );

        $this->assertTrue($commandResult->success);
        $this->assertEquals(Response::HTTP_OK, $commandResult->statusCode);
    }

    public function testHandleWhenBusDispatchesMoreThanOneStamp(): void
    {
        $stamp = new HandledStamp(
            result: new CommandResult(
                success: true,
                statusCode: Response::HTTP_OK
            ),
            handlerName: 'exampleHandlerName'
        );

        $envelope = new Envelope(
            message: $stamp,
            stamps: [$stamp, $stamp]
        );

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        $commandResult = $this->commandBus->handle(
            new class implements CommandInterface {
            }
        );

        $this->assertFalse($commandResult->success);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $commandResult->statusCode);
    }

    public function testHandleWhenCommandResultIsNotProperType(): void
    {
        $stamp = new HandledStamp(
            result: new class {
            },
            handlerName: 'exampleHandlerName'
        );

        $envelope = new Envelope(
            message: $stamp,
            stamps: [$stamp]
        );

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        $commandResult = $this->commandBus->handle(
            new class implements CommandInterface {
            }
        );

        $this->assertFalse($commandResult->success);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $commandResult->statusCode);
    }
}
