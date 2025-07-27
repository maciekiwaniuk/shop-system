<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Domain\Entity;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Enum\UserRole;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Uid\Uuid;

#[Group('unit')]
class UserTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_should_create_a_user_with_valid_initial_values(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $this->assertNotNull($user->getId());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('securePassword123', $user->getPassword());
        $this->assertEquals('John', $user->getName());
        $this->assertEquals('Doe', $user->getSurname());
        $this->assertNotNull($user->getCreatedAt());
        $this->assertNotNull($user->getUpdatedAt());
    }

    #[Test]
    public function it_should_generate_uuid_when_no_id_provided(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $this->assertNotEmpty($user->getId());
        $this->assertTrue(Uuid::isValid($user->getId()));
    }

    #[Test]
    public function it_should_use_provided_id_when_specified(): void
    {
        $customId = 'custom-uuid-12345';
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
            id: $customId,
        );

        $this->assertEquals($customId, $user->getId());
    }

    #[Test]
    public function it_should_have_default_user_role(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $this->assertEquals([UserRole::USER->value], $user->getRoles());
        $this->assertFalse($user->isAdmin());
    }

    #[Test]
    public function it_should_set_roles_correctly(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $newRoles = [UserRole::ADMIN->value, UserRole::USER->value];
        $user->setRoles($newRoles);

        $this->assertEquals($newRoles, $user->getRoles());
        $this->assertTrue($user->isAdmin());
    }

    #[Test]
    public function it_should_detect_admin_role(): void
    {
        $user = new User(
            email: 'admin@example.com',
            password: 'securePassword123',
            name: 'Admin',
            surname: 'User',
        );

        $user->setRoles([UserRole::ADMIN->value]);

        $this->assertTrue($user->isAdmin());
        $this->assertContains(UserRole::ADMIN->value, $user->getRoles());
    }

    #[Test]
    public function it_should_not_detect_admin_role_for_regular_user(): void
    {
        $user = new User(
            email: 'user@example.com',
            password: 'securePassword123',
            name: 'Regular',
            surname: 'User',
        );

        $this->assertFalse($user->isAdmin());
        $this->assertNotContains(UserRole::ADMIN->value, $user->getRoles());
    }

    #[Test]
    public function it_should_update_password(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'oldPassword123',
            name: 'John',
            surname: 'Doe',
        );

        $newPassword = 'newSecurePassword456';
        $user->setPassword($newPassword);

        $this->assertEquals($newPassword, $user->getPassword());
    }

    #[Test]
    public function it_should_return_id_as_user_identifier(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $this->assertEquals($user->getId(), $user->getUserIdentifier());
    }

    #[Test]
    public function it_should_generate_unique_ids_for_different_users(): void
    {
        $user1 = new User(
            email: 'user1@example.com',
            password: 'password1',
            name: 'User1',
            surname: 'Test',
        );
        $user2 = new User(
            email: 'user2@example.com',
            password: 'password2',
            name: 'User2',
            surname: 'Test',
        );

        $this->assertNotEquals($user1->getId(), $user2->getId());
        $this->assertNotEmpty($user1->getId());
        $this->assertNotEmpty($user2->getId());
    }

    #[Test]
    #[DataProvider('roleCombinationsProvider')]
    public function it_should_correctly_identify_admin_status(array $roles, bool $expectedIsAdmin): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $user->setRoles($roles);

        $this->assertEquals($expectedIsAdmin, $user->isAdmin());
    }

    public static function roleCombinationsProvider(): array
    {
        return [
            'admin only' => [[UserRole::ADMIN->value], true],
            'user only' => [[UserRole::USER->value], false],
            'admin and user' => [[UserRole::ADMIN->value, UserRole::USER->value], true],
            'multiple non-admin roles' => [['ROLE_MODERATOR', 'ROLE_EDITOR'], false],
            'empty roles' => [[], false],
        ];
    }

    #[Test]
    public function it_should_have_readonly_properties_after_construction(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $this->assertNotNull($user->getId());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('John', $user->getName());
        $this->assertEquals('Doe', $user->getSurname());
        $this->assertNotNull($user->getCreatedAt());
    }

    #[Test]
    public function it_should_implement_user_interface_correctly(): void
    {
        $user = new User(
            email: 'test@example.com',
            password: 'securePassword123',
            name: 'John',
            surname: 'Doe',
        );

        $this->assertIsArray($user->getRoles());
        $this->assertIsString($user->getUserIdentifier());
        $this->assertIsString($user->getPassword());
        $this->assertIsString($user->getPassword());
        $this->assertIsCallable([$user, 'eraseCredentials']);
    }
}
