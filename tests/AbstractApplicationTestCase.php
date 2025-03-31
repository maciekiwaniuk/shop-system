<?php

declare(strict_types=1);

namespace App\Tests;

use App\Common\Infrastructure\Cache\CacheCreator;
use App\Common\Infrastructure\Doctrine\DataFixtures\AppFixtures;
use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Enum\UserRole;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
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
    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

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

    private function clearCache(): void
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

    public function getGuestBrowser(): KernelBrowser
    {
        return $this->client;
    }
}
