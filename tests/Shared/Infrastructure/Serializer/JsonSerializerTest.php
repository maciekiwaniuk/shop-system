<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\Serializer;

use App\Shared\Infrastructure\Serializer\JsonSerializer;
use App\Tests\AbstractUnitTestCase;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

class JsonSerializerTest extends AbstractUnitTestCase
{
    protected readonly JsonSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = self::getContainer()->get(JsonSerializer::class);
    }

    public function testSerializeWithDefaultGroup(): void
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
            $serializedObject
        );
    }

    public function testSerializeWithExistingGroup(): void
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
            $serializedObject
        );
    }

    public function testSerializeWithNonExistingGroup(): void
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
            $serializedObject
        );
    }

    public function testDeserializeWithDefaultGroup(): void
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
            get_class($object)
        );

        $this->assertEquals(
            $object->name,
            $deserializedData->name
        );
    }

    public function testDeserializeWithExistingGroup(): void
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
            ['groups' => ['existing-group']]
        );

        $this->assertEquals(
            $object->name,
            $deserializedData->name
        );
    }

    public function testDeserializeWithNonExistingGroup(): void
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
            get_class($object)
        );
    }
}
