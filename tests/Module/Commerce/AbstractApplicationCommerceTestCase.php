<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Tests\AbstractApplicationTestCase;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

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

    public function insertProduct(?Product $product = null): Product
    {
        if ($product === null) {
            $product = new Product(
                name: 'productFixtureName-' . substr(Ulid::generate(), 0, 6),
                price: random_int(1, 100),
            );
        }

        $productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
        $productRepository->save($product, true);

        return $product;
    }
}
