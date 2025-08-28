<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\DTO\Validation;

use App\Module\Commerce\Application\DTO\Validation\ChangeOrderStatusDTO;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Group('integration')]
class ChangeOrderStatusDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    #[Test]
    public function it_should_pass_validation_when_status_is_valid(): void
    {
        $dto = new ChangeOrderStatusDTO(status: OrderStatus::SENT);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_status_is_waiting_for_payment(): void
    {
        $dto = new ChangeOrderStatusDTO(status: OrderStatus::WAITING_FOR_PAYMENT);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_status_is_preparing_for_delivery(): void
    {
        $dto = new ChangeOrderStatusDTO(status: OrderStatus::PREPARING_FOR_DELIVERY);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_status_is_in_delivery(): void
    {
        $dto = new ChangeOrderStatusDTO(status: OrderStatus::SENT);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_status_is_delivered(): void
    {
        $dto = new ChangeOrderStatusDTO(status: OrderStatus::DELIVERED);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_status_is_cancelled(): void
    {
        $dto = new ChangeOrderStatusDTO(status: OrderStatus::CANCELLED);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_not_pass_validation_when_status_is_null(): void
    {
        $dto = new ChangeOrderStatusDTO(status: null);

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    #[Test]
    public function it_should_return_correct_error_message_for_null_status(): void
    {
        $dto = new ChangeOrderStatusDTO(status: null);

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('Order status cannot be blank.', $errors[0]->getMessage());
    }
}
