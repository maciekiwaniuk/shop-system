<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class AbstractApplicationTestCase extends WebTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Execution possible only in testing environment');
        }
        $this->initDatabase($kernel);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    private function initDatabase(KernelInterface $kernel): void
    {
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        if ($entityManager instanceof EntityManagerInterface) {
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->updateSchema($metaData);
        }
    }
}
