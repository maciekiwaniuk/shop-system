<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\Bus\SyncCommandBus;

use App\Common\Application\Bus\SyncCommandBus\SyncCommandBus;
use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Tests\AbstractUnitTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class SyncCommandBusTest extends AbstractUnitTestCase
{
    private MessageBusInterface $bus;
    private LoggerInterface $logger;
    private SyncCommandBusInterface $syncCommandBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->syncCommandBus = new SyncCommandBus(
            $this->bus,
            $this->logger,
        );
    }

    /** @test */
    public function will_handle(): void
    {
        $stamp = new HandledStamp(
            result: new CommandResult(
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

        $commandResult = $this->syncCommandBus->handle(
            new class implements SyncCommandInterface {
            },
        );

        $this->assertTrue($commandResult->success);
        $this->assertEquals(Response::HTTP_OK, $commandResult->statusCode);
    }

    /** @test */
    public function will_fail_when_bus_dispatches_more_than_one_stamp(): void
    {
        $stamp = new HandledStamp(
            result: new CommandResult(
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

        $commandResult = $this->syncCommandBus->handle(
            new class implements SyncCommandInterface {
            },
        );

        $this->assertFalse($commandResult->success);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $commandResult->statusCode);
    }

    /** @test */
    public function will_fail_when_command_bus_is_not_proper_type(): void
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

        $commandResult = $this->syncCommandBus->handle(
            new class implements SyncCommandInterface {
            },
        );

        $this->assertFalse($commandResult->success);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $commandResult->statusCode);
    }
}
