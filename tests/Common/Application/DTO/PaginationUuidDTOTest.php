<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\DTO;

use App\Common\Application\DTO\PaginationUuidDTO;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Group('integration')]
class PaginationUuidDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;
    private string $exampleValidCursor;
    private int $exampleValidLimit = 10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->exampleValidCursor = Uuid::v4()->toString();
    }

    #[Test]
    public function it_can_pass_valid_data(): void
    {
        $dto = new PaginationUuidDTO(
            cursor: $this->exampleValidCursor,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_can_pass_with_null_cursor(): void
    {
        $dto = new PaginationUuidDTO(
            cursor: null,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    public static function invalidLimitProvider(): iterable
    {
        yield 'zero limit' => [0];
        yield 'negative limit' => [-300];
    }

    #[Test]
    #[DataProvider('invalidLimitProvider')]
    public function it_can_detect_invalid_limit(int $limit): void
    {
        $dto = new PaginationUuidDTO(
            cursor: $this->exampleValidCursor,
            limit: $limit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('limit', $errors->get(0)->getPropertyPath());
    }

    public static function invalidCursorProvider(): iterable
    {
        yield 'not a uuid' => ['not-a-uuid'];
    }

    #[Test]
    #[DataProvider('invalidCursorProvider')]
    public function it_can_detect_invalid_cursor(mixed $cursor): void
    {
        $dto = new PaginationUuidDTO(
            cursor: $cursor,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('cursor', $errors->get(0)->getPropertyPath());
    }
}
