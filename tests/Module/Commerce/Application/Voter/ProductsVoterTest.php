<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\Voter;

use App\Common\Application\Security\UserContextInterface;
use App\Module\Commerce\Application\Voter\ProductsVoter;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProductsVoterTest extends AbstractUnitTestCase
{
    private TokenInterface $token;
    private ProductsVoter $clientVoter;
    private ProductsVoter $adminVoter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);

        $client = $this->createMock(UserContextInterface::class);
        $client
            ->method('isAdmin')
            ->willReturn(false);
        $this->clientVoter = new ProductsVoter($client);

        $admin = $this->createMock(UserContextInterface::class);
        $admin
            ->method('isAdmin')
            ->willReturn(true);
        $this->adminVoter = new ProductsVoter($admin);
    }

    /** @test */
    public function it_should_allow_client_to_get_paginated_products(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->clientVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::GET_PAGINATED,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_not_allow_client_to_create_product(): void
    {
        $this->assertFalse(
            $this->useMethod(
                object: $this->clientVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::CREATE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_admin_to_create_product(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->adminVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::CREATE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_client_to_show_product(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->clientVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::SHOW,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_not_allow_client_to_update_product(): void
    {
        $this->assertFalse(
            $this->useMethod(
                object: $this->clientVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::UPDATE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_admin_to_update_product(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->adminVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::UPDATE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_not_allow_client_to_delete_product(): void
    {
        $this->assertFalse(
            $this->useMethod(
                object: $this->clientVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::DELETE,
                    null,
                    $this->token,
                ],
            ),
        );
    }

    /** @test */
    public function it_should_allow_admin_to_delete_product(): void
    {
        $this->assertTrue(
            $this->useMethod(
                object: $this->adminVoter,
                method: 'voteOnAttribute',
                args: [
                    ProductsVoter::DELETE,
                    null,
                    $this->token,
                ],
            ),
        );
    }
}
