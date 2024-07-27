<?php

declare(strict_types=1);

namespace App\Tests\Module\User\UI\Controller;

use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1';
    protected readonly UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = self::getContainer()->get(UserRepositoryInterface::class);
    }

    public function testRegister(): void
    {
        $usersBeforeAction = count($this->userRepository->findAll());

        $client = $this->getGuestClient();
        $client->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/register',
            content: json_encode([
                'email' => 'newUser@email.com',
                'password' => 'superHardPassword',
                'name' => 'John',
                'surname' => 'Williams',
            ]),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNotNull($responseData['message']);
        $this->assertCount(
            $usersBeforeAction + 1,
            $this->userRepository->findAll(),
        );
    }
}
