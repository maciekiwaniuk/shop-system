<?php

declare(strict_types=1);

namespace App\Tests\Module\Product\Application\Voter;

use App\Module\Product\Application\Voter\ProductsVoter;
use App\Module\User\Domain\Entity\User;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoterTest extends AbstractUnitTestCase
{
    protected ProductsVoter $voter;
    protected TokenInterface $token;
    protected UserInterface $user;
    protected UserInterface $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->voter = new ProductsVoter();
        $this->token = $this->createMock(TokenInterface::class);
        $this->user = $this->getMockBuilder(UserInterface::class)
            ->addMethods(['isAdmin'])
            ->getMock();
        $this->user
            ->method('isAdmin')
            ->willReturn(false);
        $this->admin = $this->getMockBuilder(UserInterface::class)
            ->addMethods(['isAdmin'])
            ->getMock();
        $this->admin
            ->method('isAdmin')
            ->willReturn(true);
    }

    public function testUserCanGetAll(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->voter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::GET_ALL,
                    null,
                    $this->user
                ]
            )
        );
    }

//    public function testUserCantNew(): void
//    {
//
//    }
//
//    public function testAdminCanNew(): void
//    {
//
//    }
//
//    public function testUserCanShow(): void
//    {
//
//    }
//
//    public function testUserCantUpdate(): void
//    {
//
//    }
//
//    public function testAdminCanUpdate(): void
//    {
//
//    }
//
//    public function testUserCantDelete(): void
//    {
//
//    }
//
//    public function testAdminCanDelete(): void
//    {
//
//    }
}
