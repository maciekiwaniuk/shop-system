<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractUnitTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }
}