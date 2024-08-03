<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\DTO;

use App\Common\Application\DTO\PaginationIdDTO;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaginationIdDTOTest extends AbstractIntegrationTestCase
{
    protected readonly ValidatorInterface $validator;
    protected int $exampleValidOffset = 1;
    protected int $exampleValidLimit = 10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new PaginationIdDTO(
            offset: $this->exampleValidOffset,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    public function invalidOffsetProvider(): iterable
    {
        yield [0];
        yield [-10];
    }

    /**
     * @dataProvider invalidOffsetProvider
     */
    public function testInvalidOffset(int $offset): void
    {
        $dto = new PaginationIdDTO(
            offset: $offset,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function invalidLimitProvider(): iterable
    {
        yield [0];
        yield [-300];
    }

    /**
     * @dataProvider invalidLimitProvider
     */
    public function testInvalidLimit(int $limit): void
    {
        $dto = new PaginationIdDTO(
            offset: $this->exampleValidOffset,
            limit: $limit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
