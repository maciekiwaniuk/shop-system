<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class AbstractApplicationTestCase extends WebTestCase
{
    protected ?KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = self::createClient();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        if ('test' !== $kernel->getEnvironment()) {
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
        return $this->client;
    }

    public function getAdminClient(): KernelBrowser
    {
        return $this->client;
    }
}
