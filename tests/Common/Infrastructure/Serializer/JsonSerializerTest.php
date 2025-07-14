<?php

declare(strict_types=1);

namespace App\Tests\Common\Infrastructure\Serializer;

use App\Common\Infrastructure\Serializer\JsonSerializer;
use App\Tests\AbstractUnitTestCase;
use Exception;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Serializer\Annotation\Groups;

#[Group('unit')]
class JsonSerializerTest extends AbstractUnitTestCase
{
    private JsonSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = self::getContainer()->get(JsonSerializer::class);
    }

    #[Test]
    public function it_should_serialize_object_with_default_group(): void
    {
        $object = new class ('exampleName') {
            #[Groups('default')]
            public string $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        };

        $serializedObject = $this->serializer->serialize($object);

        $this->assertEquals(
            json_encode(['name' => 'exampleName']),
            $serializedObject,
        );
    }

    #[Test]
    public function it_should_serialize_object_with_existing_group(): void
    {
        $object = new class ('exampleName') {
            #[Groups('example-group')]
            public string $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        };

        $serializedObject = $this->serializer->serialize($object, ['groups' => ['example-group']]);

        $this->assertEquals(
            json_encode(['name' => 'exampleName']),
            $serializedObject,
        );
    }

    #[Test]
    public function it_should_return_empty_array_when_serializing_object_with_non_existing_group(): void
    {
        $object = new class ('exampleName') {
            #[Groups('non-existing-group')]
            public string $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        };

        $serializedObject = $this->serializer->serialize($object);

        $this->assertEquals(
            '[]',
            $serializedObject,
        );
    }

    #[Test]
    public function it_should_deserialize_object_with_default_group(): void
    {
        $object = new class ('exampleName') {
            #[Groups(['default'])]
            public string $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        };

        $deserializedData = $this->serializer->deserialize(
            json_encode(['name' => 'exampleName']),
            get_class($object),
        );

        $this->assertEquals(
            $object->name,
            $deserializedData->name,
        );
    }

    #[Test]
    public function it_should_deserialize_object_with_existing_group(): void
    {
        $object = new class ('exampleName') {
            #[Groups(['existing-group'])]
            public string $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        };

        $deserializedData = $this->serializer->deserialize(
            json_encode(['name' => 'exampleName']),
            get_class($object),
            ['groups' => ['existing-group']],
        );

        $this->assertEquals(
            $object->name,
            $deserializedData->name,
        );
    }

    #[Test]
    public function it_should_throw_exception_when_deserializing_object_with_non_existing_group(): void
    {
        $object = new class ('exampleName') {
            #[Groups(['non-existing-group'])]
            public string $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        };

        $this->expectException(Exception::class);

        $this->serializer->deserialize(
            json_encode(['name' => 'exampleName']),
            get_class($object),
        );
    }
}
