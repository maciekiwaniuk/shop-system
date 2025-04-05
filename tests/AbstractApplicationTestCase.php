<?php

declare(strict_types=1);

namespace App\Tests;

use App\Common\Infrastructure\Cache\CacheCreator;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractApplicationTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $authEntityManager;
    protected EntityManagerInterface $commerceEntityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        self::ensureKernelShutdown();

        if ('test' !== self::$kernel->getEnvironment()) {
            throw new LogicException('Execution is possible only in test environment');
        }

        $this->client = self::createClient();

        $this->authEntityManager = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager('auth');
        $schemaTool = new SchemaTool($this->authEntityManager);
        $schemaTool->updateSchema(
            $this->authEntityManager->getMetadataFactory()->getAllMetadata(),
        );

        $this->commerceEntityManager = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager('commerce');
        $schemaTool = new SchemaTool($this->commerceEntityManager);
        $schemaTool->updateSchema(
            $this->commerceEntityManager->getMetadataFactory()->getAllMetadata(),
        );

//        $this->addFixture(
//            className: AppFixtures::class,
//            classesToInjectToFixture: [
//                UserPasswordHasherInterface::class,
//            ],
//        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->clearCache();

        $purger = new ORMPurger($this->authEntityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();

        $purger = new ORMPurger($this->commerceEntityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
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

        $purger = new ORMPurger($this->authEntityManager);
        $executor = new ORMExecutor($this->authEntityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function getGuestBrowser(): KernelBrowser
    {
        return $this->client;
    }
}
