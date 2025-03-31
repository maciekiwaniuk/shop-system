<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce;

use App\Module\Commerce\Domain\Entity\Client;
use App\Tests\AbstractApplicationTestCase;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AbstractApplicationCommerceTestCase extends AbstractApplicationTestCase
{
    public function getClientBrowser(?Client $client = null): KernelBrowser
    {
        $this->client->request(
            method: 'POST',
            uri: '/api/v1/register',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => isset($client) ? $client->getEmail() : 'example@email.com',
                'password' => 'examplePassword',
                'name' => 'exampleName',
                'surname' => 'exampleSurname',
            ]),
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter(
            'HTTP_Authorization',
            sprintf('Bearer %s', $data['data']['token']),
        );
        return $this->client;
    }

    public function getAdminBrowser(?Client $client = null): KernelBrowser
    {
        $this->client->request(
            method: 'POST',
            uri: '/api/v1/register',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => isset($client) ? $client->getEmail() : 'example@email.com',
                'password' => 'examplePassword',
                'name' => 'exampleName',
                'surname' => 'exampleSurname',
            ]),
        );
        $email = isset($client) ? $client->getEmail() : 'example@email.com';

        /** @var Connection $connection */
        $connection = self::getContainer()->get(Connection::class);

        $connection->executeStatement(
            'UPDATE shop_system_auth_test.user SET roles = :roles WHERE email = :email',
            [
                'roles' => json_encode(['ROLE_USER', 'ROLE_ADMIN']),
                'email' => $email,
            ]
        );

        $this->client->request(
            method: 'POST',
            uri: '/api/v1/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => $email,
                'password' => 'examplePassword',
            ]),
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter(
            'HTTP_Authorization',
            sprintf('Bearer %s', $data['data']['token']),
        );
        return $this->client;
    }
}
