<?php

declare(strict_types=1);

namespace App\Common\Domain\Serializer;

interface JsonSerializerInterface
{
    /**
     * @param array{groups: array<string>} $context
     */
    public function serialize(mixed $data, array $context = ['groups' => ['default']]): string;

    /**
     * @param array{groups: array<string>} $context
     */
    public function deserialize(mixed $data, string $type, array $context = ['groups' => ['default']]): mixed;
}
