<?php

declare(strict_types=1);

namespace Shared\Application\DTO;

use App\Common\Application\DTO\PaginationUuidDTO;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaginationUuidDTOTest extends AbstractIntegrationTestCase
{
    protected readonly ValidatorInterface $validator;
    protected string $exampleValidCursor = 'exampleUuidCursor';
    protected int $exampleValidLimit = 10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new PaginationUuidDTO(
            cursor: $this->exampleValidCursor,
            limit: $this->exampleValidLimit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
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
        $dto = new PaginationUuidDTO(
            cursor: $this->exampleValidCursor,
            limit: $limit,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
