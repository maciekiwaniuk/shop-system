<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractUnitTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }
}
