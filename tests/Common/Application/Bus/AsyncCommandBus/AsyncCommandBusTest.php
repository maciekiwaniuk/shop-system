<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\Bus\AsyncCommandBus;

use App\Common\Application\AsyncCommand\AsyncCommandInterface;
use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBus;
use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBusInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class AsyncCommandBusTest extends AbstractUnitTestCase
{
    private MessageBusInterface $bus;
    private AsyncCommandBusInterface $asyncCommandBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->asyncCommandBus = new AsyncCommandBus($this->bus);
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

        $this->asyncCommandBus->handle(
            new class implements AsyncCommandInterface {
            }
        );
    }
}
