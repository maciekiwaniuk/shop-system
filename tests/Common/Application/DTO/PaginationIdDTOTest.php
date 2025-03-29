<?php

declare(strict_types=1);

namespace App\Tests\Common\Application\DTO;

use App\Common\Application\DTO\PaginationIdDTO;
use App\Tests\AbstractIntegrationTestCase;
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

    /** @test */
    public function it_can_pass_valid_data(): void
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
     * @test
     */
    public function it_can_detect_invalid_offset(int $offset): void
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
     * @test
     */
    public function it_can_detect_invalid_limit(int $limit): void
    {
        $dto = new PaginationIdDTO(
            offset: $this->exampleValidOffset,
            limit: $limit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
