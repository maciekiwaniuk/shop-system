<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\Voter;

use App\Module\Commerce\Application\Voter\OrdersVoter;
use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Auth\Domain\Entity\User;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OrdersVoterTest extends AbstractUnitTestCase
{
    private OrdersVoter $voter;
    private TokenInterface $token;
    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->voter = new OrdersVoter();
        $this->token = $this->createMock(TokenInterface::class);

        $this->user = $this->createMock(User::class);
        $this->user
            ->method('isAdmin')
            ->willReturn(false);

        $this->admin = $this->createMock(User::class);
        $this->admin
            ->method('isAdmin')
            ->willReturn(true);
    }

    public function testUserCantGetPaginated(): void
    {
        $this->token
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertFalse(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::GET_PAGINATED,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    public function testAdminCanGetPaginated(): void
    {
        $this->token
            ->method('getUser')
            ->willReturn($this->admin);

        $this->assertTrue(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::GET_PAGINATED,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    public function testUserNotOwningOrderCantShow(): void
    {
        $order = $this->createMock(Order::class);
        $order
            ->method('getUser')
            ->willReturn($this->admin);

        $this->token
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertFalse(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::SHOW,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    public function testUserOwningOrderCanShow(): void
    {
        $order = $this->createMock(Order::class);
        $order
            ->method('getUser')
            ->willReturn($this->user);

        $this->token
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertTrue(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::SHOW,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    public function testAdminShow(): void
    {
        $order = $this->createMock(Order::class);
        $order
            ->method('getUser')
            ->willReturn($this->user);

        $this->token
            ->method('getUser')
            ->willReturn($this->admin);

        $this->assertTrue(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::SHOW,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    public function testUserCanCreate(): void
    {
        $this->token
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertTrue(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::CREATE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    public function testUserCantUpdateStatus(): void
    {
        $order = $this->createMock(Order::class);
        $order
            ->method('getUser')
            ->willReturn($this->user);

        $this->token
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertFalse(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::UPDATE_STATUS,
                    $order,
                    $this->token,
                ],
            ),
        );
    }

    public function testAdminCanUpdateStatus(): void
    {
        $order = $this->createMock(Order::class);
        $order
            ->method('getUser')
            ->willReturn($this->user);

        $this->token
            ->method('getUser')
            ->willReturn($this->admin);

        $this->assertTrue(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    OrdersVoter::UPDATE_STATUS,
                    $order,
                    $this->token,
                ],
            ),
        );
    }
}
