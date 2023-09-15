<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email.')]
class User
{
    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['default'])]
    private string $id;

    #[ORM\Column(length: 200, unique: true)]
    #[Groups(['default'])]
    private string $email;

    /**
     * @var string[]
     */
    #[ORM\Column]
    #[Groups(['default'])]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['default'])]
    private string $password;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['default'])]
    private string $name;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['default'])]
    private string $surname;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['default'])]
    private ?string $lastLoginIp = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['default'])]
    private ?string $lastLoginTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['default'])]
    private ?string $lastLoginUserAgent = null;

    #[ORM\Column]
    #[Groups(['default'])]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column]
    #[Groups(['default'])]
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $email,
        string $password,
        string $name,
        string $surname
    ) {
        $this->id = (string) Uuid::v4();
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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
        if (in_array('ROLE_ADMIN', $this->roles)) {
            return true;
        }
        return false;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginTime(): ?string
    {
        return $this->lastLoginTime;
    }

    public function setLastLoginTime(string $lastLoginTime): self
    {
        $this->lastLoginTime = $lastLoginTime;

        return $this;
    }

    public function getLastLoginUserAgent(): ?string
    {
        return $this->lastLoginUserAgent;
    }

    public function setLastLoginUserAgent(string $lastLoginUserAgent): self
    {
        $this->lastLoginUserAgent = $lastLoginUserAgent;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
