<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\DTO\Validation;

use App\Module\Commerce\Application\DTO\Validation\SearchProductsDTO;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Group('integration')]
class SearchProductsDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    #[Test]
    public function it_should_pass_validation_when_phrase_is_minimum_length(): void
    {
        $dto = new SearchProductsDTO(phrase: 'ab');

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_phrase_is_maximum_length(): void
    {
        $dto = new SearchProductsDTO(phrase: str_repeat('a', 100));

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_phrase_contains_special_characters(): void
    {
        $dto = new SearchProductsDTO(phrase: 'search with !@#$%^&*() characters');

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_phrase_contains_numbers(): void
    {
        $dto = new SearchProductsDTO(phrase: 'search with 123 numbers');

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_phrase_contains_unicode_characters(): void
    {
        $dto = new SearchProductsDTO(phrase: 'search with éñüß characters');

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    public static function invalidPhraseProvider(): array
    {
        return [
            'empty string' => [''],
            'single character' => ['a'],
            'too long' => [str_repeat('a', 101)],
            'only whitespace' => ['   '],
            'tab only' => ["\t"],
            'newline only' => ["\n"],
        ];
    }

    #[Test]
    public function it_should_return_correct_error_message_for_blank_phrase(): void
    {
        $dto = new SearchProductsDTO(phrase: '');

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('Search phrase cannot be blank.', $errors[0]->getMessage());
    }

    #[Test]
    public function it_should_return_correct_error_message_for_too_short_phrase(): void
    {
        $dto = new SearchProductsDTO(phrase: 'a');

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('Search phrase must be at least 2 characters long.', $errors[0]->getMessage());
    }

    #[Test]
    public function it_should_return_correct_error_message_for_too_long_phrase(): void
    {
        $dto = new SearchProductsDTO(phrase: str_repeat('a', 101));

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('Search phrase cannot be longer than 100 characters.', $errors[0]->getMessage());
    }
}
