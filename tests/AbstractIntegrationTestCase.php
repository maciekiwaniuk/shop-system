<?php

declare(strict_types=1);

namespace App\Tests;

use App\Shared\Infrastructure\Cache\CacheCreator;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractIntegrationTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

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
}
