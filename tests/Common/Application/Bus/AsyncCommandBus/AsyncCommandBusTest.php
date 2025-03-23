<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\Bus\AsyncCommandBus;

use App\Common\Application\AsyncCommand\AsyncCommandInterface;
use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBus;
use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBusInterface;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

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
        $this->bus
            ->expects($this->once())
            ->method('dispatch');

        $this->asyncCommandBus->handle(
            new class implements AsyncCommandInterface {
            }
        );
    }
}
