<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Interface\Controller;

use App\Module\Auth\Domain\Entity\User;
use App\Tests\Module\Auth\AbstractApplicationAuthTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtLexikLoginEndpointTest extends AbstractApplicationAuthTestCase
{
    private string $url = '/api/v1';

    #[Test]
    public function can_login_with_valid_credentials(): void
    {
        $user = $this->insertUser();
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => $user->getEmail(),
                'password' => 'examplePassword',
            ])
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('token', $responseData['data']);
        $this->assertNotEmpty($responseData['data']['token']);
    }

    #[Test]
    public function cannot_login_with_invalid_credentials(): void
    {
        $user = $this->insertUser();
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => $user->getEmail(),
                'password' => 'wrongPassword',
            ]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Invalid credentials', $responseData['message']);
    }

    #[Test]
    public function cannot_login_with_nonexistent_user_credentials(): void
    {
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => 'nonexistent@example.com',
                'password' => 'examplePassword',
            ]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Invalid credentials', $responseData['message']);
    }

    #[Test]
    public function cannot_login_with_missing_credentials(): void
    {
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => 'test@example.com',
            ]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function cannot_login_with_invalid_json(): void
    {
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: 'invalid json',
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function can_login_with_custom_user_credentials(): void
    {
        $customUser = new User(
            email: 'custom@example.com',
            password: 'customPassword',
            name: 'Custom',
            surname: 'User',
        );
        $user = $this->insertUser($customUser);
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => $user->getEmail(),
                'password' => 'customPassword',
            ]),
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('token', $responseData['data']);
        $this->assertNotEmpty($responseData['data']['token']);
    }

    #[Test]
    public function cannot_login_with_empty_credentials(): void
    {
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => 'notMatching@email.com',
                'password' => 'wrongPassword',
            ]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Invalid credentials', $responseData['message']);
    }
}
