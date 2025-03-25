<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\Voter;

use App\Common\Application\Security\UserContextInterface;
use App\Module\Commerce\Application\Voter\OrdersVoter;
use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Order;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OrdersVoterTest extends AbstractUnitTestCase
{
    private TokenInterface $token;
    private string $clientOneId;
    private string $clientTwoId;
    private OrdersVoter $clientOneVoter;
    private OrdersVoter $adminVoter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);

        $this->clientOneId = 'clientOneId';
        $clientOne = $this->createMock(UserContextInterface::class);
        $clientOne
            ->method('isAdmin')
            ->willReturn(false);
        $clientOne
            ->method('getUserIdentifier')
            ->willReturn($this->clientOneId);
        $this->clientOneVoter = new OrdersVoter($clientOne);

        $this->clientTwoId = 'clientTwoId';
        $clientTwo = $this->createMock(UserContextInterface::class);
        $clientTwo
            ->method('isAdmin')
            ->willReturn(false);
        $clientTwo
            ->method('getUserIdentifier')
            ->willReturn($this->clientTwoId);

        $admin = $this->createMock(UserContextInterface::class);
        $admin
            ->method('isAdmin')
            ->willReturn(true);
        $this->adminVoter = new OrdersVoter($admin);
    }

    /** @test */
    public function it_should_allow_client_to_get_paginated_orders(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->clientOneVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::GET_PAGINATED,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_not_allow_client_to_show_somebody_else_order(): void
    {
        $clientTwo = $this->createMock(Client::class);
        $clientTwo
            ->method('getId')
            ->willReturn($this->clientTwoId);
        $order = $this->createMock(Order::class);
        $order
            ->method('getClient')
            ->willReturn($clientTwo);

        $this->assertFalse(
            $this->useMethod(
                object: $this->clientOneVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::SHOW,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_client_to_show_owned_order(): void
    {
        $clientOne = $this->createMock(Client::class);
        $clientOne
            ->method('getId')
            ->willReturn($this->clientOneId);
        $order = $this->createMock(Order::class);
        $order
            ->method('getClient')
            ->willReturn($clientOne);

        $this->assertTrue(
            $this->useMethod(
                object: $this->clientOneVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::SHOW,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_admin_to_show_somebody_else_order(): void
    {
        $clientOne = $this->createMock(Client::class);
        $clientOne
            ->method('getId')
            ->willReturn($this->clientOneId);
        $order = $this->createMock(Order::class);
        $order
            ->method('getClient')
            ->willReturn($clientOne);

        $this->assertTrue(
            $this->useMethod(
                object: $this->adminVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::SHOW,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_to_create_order(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->clientOneVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::CREATE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_not_allow_client_to_update_order_status(): void
    {
        $this->assertFalse(
            $this->useMethod(
                object: $this->clientOneVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::UPDATE_STATUS,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_admin_to_update_order_status(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->adminVoter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::UPDATE_STATUS,
                    null,
                    $this->token,
                ],
            ),
        );
    }
}
