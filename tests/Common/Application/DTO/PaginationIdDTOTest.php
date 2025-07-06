<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\DTO;

use App\Common\Application\DTO\PaginationIdDTO;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaginationIdDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;
    private int $exampleValidOffset = 1;
    private int $exampleValidLimit = 10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    #[Test]
    public function it_can_pass_valid_data(): void
    {
        $dto = new PaginationIdDTO(
            offset: $this->exampleValidOffset,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    public static function invalidOffsetProvider(): iterable
    {
        yield [0];
        yield [-10];
    }

    #[Test]
    #[DataProvider('invalidOffsetProvider')]
    public function it_can_detect_invalid_offset(int $offset): void
    {
        $dto = new PaginationIdDTO(
            offset: $offset,
            limit: 10,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public static function invalidLimitProvider(): iterable
    {
        yield [0];
        yield [-300];
    }

    #[Test]
    #[DataProvider('invalidLimitProvider')]
    public function it_can_detect_invalid_limit(int $limit): void
    {
        $dto = new PaginationIdDTO(
            offset: 1,
            limit: $limit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
