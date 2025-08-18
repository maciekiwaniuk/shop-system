<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClientRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(
    name: 'client_search_idx',
    columns: ['email'],
)]
#[ORM\Table(name: '`client`')]
#[UniqueEntity(fields: ['email'])]
class Client
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['default'])]
    private readonly string $id;

    #[ORM\Column(length: 200, unique: true)]
    #[Groups(['default'])]
    private string $email;

    #[ORM\Column(length: 100)]
    #[Groups(['default'])]
    private string $name;

    #[ORM\Column(length: 100)]
    #[Groups(['default'])]
    private string $surname;

    #[ORM\Column(name: 'updated_at', length: 255)]
    #[Groups(['default'])]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(name: 'created_at', length: 255)]
    #[Groups(['default'])]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $email,
        string $name,
        string $surname,
    ) {
        $this->id = $id;
        $this->email = $email;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
