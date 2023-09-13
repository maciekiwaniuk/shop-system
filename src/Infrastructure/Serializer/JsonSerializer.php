<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class JsonSerializer
{
    protected SerializerInterface $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()]
        );
    }

    public function serialize(mixed $data, array $groups = ['default']): string
    {
        return $this->serializer->serialize($data, 'json', $groups);
    }

    public function deserialize(mixed $data, string $type, array $groups = ['default']): mixed
    {
        return $this->serializer->deserialize($data, $type, 'json', $groups);
    }
}