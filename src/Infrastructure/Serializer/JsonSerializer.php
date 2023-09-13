<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class JsonSerializer
{
    protected SerializerInterface $serializer;

    public function __construct()
    {
        $dateTimeNormalizer = new DateTimeNormalizer();
        $dateTimeNormalizer
            ->setDefaultContext([
                DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
                DateTimeNormalizer::TIMEZONE_KEY => null
            ]);

        $this->serializer = new Serializer(
            [$dateTimeNormalizer, new ObjectNormalizer()],
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