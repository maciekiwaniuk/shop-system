<?php

declare(strict_types=1);

namespace App\Tests;

use App\Module\User\Domain\Entity\User;
use App\Module\User\Infrastructure\Doctrine\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AbstractApplicationTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = self::createClient();

        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        if ('test' !== self::$kernel->getEnvironment()) {
            throw new LogicException('Execution possible only in testing environment');
        }

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema(
            $this->entityManager->getMetadataFactory()->getAllMetadata()
        );

        $this->entityManager->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->entityManager->rollback();

        parent::tearDown();
    }

    public function getGuestClient(): KernelBrowser
    {
        return $this->client;
    }

    public function getUserClient(): KernelBrowser
    {
//        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
//        $unhashedPassword = 'examplePassword';
//        $user = new User(
//            email: 'exampleEmail@email.com',
//            password: $unhashedPassword,
//            name: 'exampleName',
//            surname: 'exampleSurname'
//        );
//        $user->setPassword(
//            $passwordHasher->hashPassword($user, $unhashedPassword)
//        );
//
//        $userRepository = self::getContainer()->get(UserRepository::class);
//        $userRepository->save($user, true);

//        $this->client->request(
//            method: 'POST',
//            uri: '/api/v1/login',
//            parameters: [
//                'headers' => [
//                    'Content-Type' => 'application/json'
//                ],
//                'body' => json_encode([
//                    'email' => $user->getEmail(),
//                    'password' => $unhashedPassword,
//                ])
//            ],
//        );
        $this->client->enableProfiler();
        $this->client->request(
            method: 'POST',
            uri: '/api/v1/register',
            content: json_encode([
                'email' => 'exampleEmail@email.com',
                'password' => 'examplePassword',
                'name' => 'exampleName',
                'surname' => 'exampleSurname'
            ])
        );
        var_dump($this->client->getResponse()->getContent());
        var_dump($this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);

//        $this->client->setServerParameter(
//            'HTTP_Authorization', sprintf('Bearer %s', $data['data']['token'])
//        );


//        $token = self::getContainer()->get(JWTTokenManagerInterface::class)->create($fetchedUser[0]);
//        var_dump('---token----');
//        var_dump($token);

//        $this->client->setServerParameter('HTTP_Authorization', 'Bearer '.  $token);

        return $this->client;
    }

    public function getAdminClient(): KernelBrowser
    {
        return $this->client;
    }
}
