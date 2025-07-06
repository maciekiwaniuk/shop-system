<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\DTO;

use App\Common\Application\DTO\PaginationUuidDTO;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaginationUuidDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;
    private string $exampleValidCursor = 'exampleUuidCursor';
    private int $exampleValidLimit = 10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
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

    public static function invalidLimitProvider(): iterable
    {
        yield [0];
        yield [-300];
    }

    #[Test]
    #[DataProvider('invalidLimitProvider')]
    public function it_can_detect_invalid_limit(int $limit): void
    {
        $dto = new PaginationUuidDTO(
            cursor: 'exampleUuidCursor',
            limit: $limit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
