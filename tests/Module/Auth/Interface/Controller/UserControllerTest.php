<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Interface\Controller;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Tests\Module\Auth\AbstractApplicationAuthTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends AbstractApplicationAuthTestCase
{
    private string $url = '/api/v1';
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = self::getContainer()->get(UserRepositoryInterface::class);
    }

    #[Test]
    public function can_register_new_user(): void
    {
        $usersCountBeforeAction = count($this->userRepository->findAll());
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/register',
            content: json_encode([
                'email' => 'newUser@email.com',
                'password' => 'superHardPassword',
                'name' => 'John',
                'surname' => 'Williams',
            ]),
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNotNull($responseData['message']);
        $this->assertCount(
            $usersCountBeforeAction + 1,
            $this->userRepository->findAll(),
        );
    }
}
