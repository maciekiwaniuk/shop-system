<?php

declare(strict_types=1);

namespace App\Tests;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Enum\UserRole;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Infrastructure\Cache\CacheCreator;
use App\Shared\Infrastructure\Doctrine\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AbstractApplicationTestCase extends WebTestCase
{
    protected readonly KernelBrowser $client;
    protected readonly EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = self::createClient();

        if ('test' !== self::$kernel->getEnvironment()) {
            throw new LogicException('Execution only in Test environment possible!');
        }

        $this->entityManager = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema(
            $this->entityManager->getMetadataFactory()->getAllMetadata(),
        );

        $this->addFixture(
            className: AppFixtures::class,
            classesToInjectToFixture: [
                UserPasswordHasherInterface::class,
            ],
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->clearCache();

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }

    protected function clearCache(): void
    {
        /** @var CacheCreator $cacheCreator */
        $cacheCreator = self::getContainer()->get(CacheCreator::class);
        $cache = $cacheCreator->create('');
        $cache->delByKeys(
            $cache->keysByPrefix(),
        );
    }

    /**
     * @param class-string $className
     * @param array<class-string> $classesToInjectToFixture
     */
    public function addFixture(string $className, array $classesToInjectToFixture = []): void
    {
        $instancesOfClassesToInject = array_map(
            fn($class) => self::getContainer()->get($class),
            $classesToInjectToFixture,
        );

        $loader = new Loader();
        $loader->addFixture(
            new $className(...$instancesOfClassesToInject),
        );

        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function getGuestClient(): KernelBrowser
    {
        return $this->client;
    }

    public function getUserClient(?User $user = null): KernelBrowser
    {
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $unhashedPassword = isset($user) ? $user->getPassword() : 'examplePassword';
        if (!isset($user)) {
            $user = new User(
                email: 'example@email.com',
                password: $unhashedPassword,
                name: 'exampleName',
                surname: 'exampleSurname',
            );
        }
        $user->setPassword(
            $passwordHasher->hashPassword($user, $unhashedPassword),
        );
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);
        $userRepository->save($user, true);

        $this->client->request(
            method: 'POST',
            uri: '/api/v1/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => $user->getEmail(),
                'password' => $unhashedPassword,
            ]),
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter(
            'HTTP_Authorization',
            sprintf('Bearer %s', $data['data']['token']),
        );
        return $this->client;
    }

    public function getAdminClient(): KernelBrowser
    {
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $unhashedPassword = 'examplePassword';
        $admin = new User(
            email: 'example@email.com',
            password: $unhashedPassword,
            name: 'exampleName',
            surname: 'exampleSurname',
        );
        $admin->setPassword(
            $passwordHasher->hashPassword($admin, $unhashedPassword),
        );
        $admin->setRoles(
            array_merge($admin->getRoles(), [UserRole::ADMIN->value]),
        );

        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);
        $userRepository->save($admin, true);

        $this->client->request(
            method: 'POST',
            uri: '/api/v1/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email' => $admin->getEmail(),
                'password' => $unhashedPassword,
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
