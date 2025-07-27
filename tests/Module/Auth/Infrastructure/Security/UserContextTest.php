<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Infrastructure\Security;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Enum\UserRole;
use App\Module\Auth\Infrastructure\Security\UserContext;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Group('unit')]
class UserContextTest extends AbstractUnitTestCase
{
    private TokenStorageInterface $tokenStorage;
    private TokenInterface $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
    }

    #[Test]
    public function it_should_return_true_for_admin_user(): void
    {
        $adminUser = $this->createAdminUser();
        $this->token->method('getUser')->willReturn($adminUser);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertTrue($userContext->isAdmin());
    }

    #[Test]
    public function it_should_return_false_for_regular_user(): void
    {
        $regularUser = $this->createRegularUser();
        $this->token->method('getUser')->willReturn($regularUser);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertFalse($userContext->isAdmin());
    }

    #[Test]
    public function it_should_return_false_when_no_token(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertFalse($userContext->isAdmin());
    }

    #[Test]
    public function it_should_return_false_when_token_has_no_user(): void
    {
        $this->token->method('getUser')->willReturn(null);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertFalse($userContext->isAdmin());
    }

    #[Test]
    public function it_should_return_user_identifier_for_authenticated_user(): void
    {
        $userId = 'user-uuid-123';
        $user = $this->createRegularUser($userId);
        $this->token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertEquals($userId, $userContext->getUserIdentifier());
    }

    #[Test]
    public function it_should_return_empty_string_when_no_token_for_user_identifier(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertEquals('', $userContext->getUserIdentifier());
    }

    #[Test]
    public function it_should_return_empty_string_when_token_has_no_user_for_user_identifier(): void
    {
        $this->token->method('getUser')->willReturn(null);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertEquals('', $userContext->getUserIdentifier());
    }

    #[Test]
    public function it_should_return_user_object_for_authenticated_user(): void
    {
        $user = $this->createRegularUser();
        $this->token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertSame($user, $userContext->getUser());
    }

    #[Test]
    public function it_should_return_null_when_no_token_for_get_user(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertNull($userContext->getUser());
    }

    #[Test]
    public function it_should_return_null_when_token_has_no_user_for_get_user(): void
    {
        $this->token->method('getUser')->willReturn(null);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertNull($userContext->getUser());
    }

    #[Test]
    public function it_should_handle_user_with_multiple_roles(): void
    {
        $user = $this->createUserWithRoles([UserRole::USER->value, UserRole::ADMIN->value]);
        $this->token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertTrue($userContext->isAdmin());
    }

    #[Test]
    public function it_should_handle_user_with_only_user_role(): void
    {
        $user = $this->createUserWithRoles([UserRole::USER->value]);
        $this->token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertFalse($userContext->isAdmin());
    }

    #[Test]
    public function it_should_handle_user_with_empty_roles(): void
    {
        $user = $this->createUserWithRoles([]);
        $this->token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertFalse($userContext->isAdmin());
    }

    #[Test]
    public function it_should_handle_user_with_custom_roles(): void
    {
        $user = $this->createUserWithRoles(['ROLE_MODERATOR', 'ROLE_EDITOR']);
        $this->token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($this->token);

        $userContext = new UserContext($this->tokenStorage);

        $this->assertFalse($userContext->isAdmin());
    }

    #[Test]
    public function it_should_return_correct_user_identifier_for_different_users(): void
    {
        $userIds = ['user-1', 'user-2', 'admin-user', 'test-user-123'];

        foreach ($userIds as $userId) {
            $user = $this->createRegularUser($userId);
            $token = $this->createMock(TokenInterface::class);
            $token->method('getUser')->willReturn($user);
            $tokenStorage = $this->createMock(TokenStorageInterface::class);
            $tokenStorage->method('getToken')->willReturn($token);
            $userContext = new UserContext($tokenStorage);

            $this->assertEquals($userId, $userContext->getUserIdentifier());
        }
    }
    private function createAdminUser(string $id = 'admin-uuid-123'): User
    {
        $user = new User(
            email: 'admin@example.com',
            password: 'hashedPassword',
            name: 'Admin',
            surname: 'User',
            id: $id,
        );
        $user->setRoles([UserRole::ADMIN->value]);
        return $user;
    }

    private function createRegularUser(string $id = 'user-uuid-123'): User
    {
        return new User(
            email: 'user@example.com',
            password: 'hashedPassword',
            name: 'Regular',
            surname: 'User',
            id: $id,
        );
    }

    private function createUserWithRoles(array $roles, string $id = 'user-uuid-123'): User
    {
        $user = new User(
            email: 'user@example.com',
            password: 'hashedPassword',
            name: 'Test',
            surname: 'User',
            id: $id,
        );
        $user->setRoles($roles);
        return $user;
    }
}
