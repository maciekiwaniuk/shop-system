<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractIntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }
}
