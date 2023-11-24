<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
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

        $objectNormalizer = new ObjectNormalizer(
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()))
        );

        $this->serializer = new Serializer(
            [$dateTimeNormalizer, $objectNormalizer],
            [new JsonEncoder()]
        );
    }

    /**
     * @param array{groups: array<string>} $context
     */
    public function serialize(
        mixed $data,
        array $context = ['groups' => ['default']]
    ): string {
        return $this->serializer->serialize($data, 'json', $context);
    }

    /**
     * @param array{groups: array<string>} $context
     */
    public function deserialize(
        mixed $data,
        string $type,
        array $context = ['groups' => ['default']]
    ): mixed {
        return $this->serializer->deserialize($data, $type, 'json', $context);
    }
}
