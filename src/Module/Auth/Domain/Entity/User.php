<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity;

use App\Module\Auth\Domain\Enum\UserRole;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(
    columns: ['email'],
    name: 'user_search_idx',
)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['default'])]
    private readonly string $id;

    #[ORM\Column(length: 200, unique: true)]
    #[Groups(['default'])]
    public string $email {
        get => $this->email;
    }

    #[ORM\Column]
    #[Groups(['user_password'])]
    private string $password;

    #[ORM\Column(length: 100)]
    #[Groups(['default'])]
    private string $name {
        get => $this->name;
    }

    #[ORM\Column(length: 100)]
    #[Groups(['default'])]
    private string $surname {
        get => $this->surname;
    }

    /**
     * @var string[]
     */
    #[ORM\Column]
    #[Groups(['default'])]
    private array $roles;

    #[ORM\Column]
    #[Groups(['default'])]
    private DateTimeImmutable $updatedAt {
        get => $this->updatedAt;
    }

    #[ORM\Column]
    #[Groups(['default'])]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        string $email,
        string $password,
        string $name,
        string $surname,
    ) {
        $this->id = (string) Uuid::v1();
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->roles = [UserRole::USER->value];
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array(UserRole::ADMIN->value, $this->roles);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function eraseCredentials(): void
    {
    }
}
