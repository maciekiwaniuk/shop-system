<?php

declare(strict_types=1);

namespace App\Tests;

use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractUnitTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    private function useMethod(mixed $object, string $method, array $args = []): mixed
    {
        return (new ReflectionMethod($object, $method))
            ->invokeArgs($object, $args);
    }
}
