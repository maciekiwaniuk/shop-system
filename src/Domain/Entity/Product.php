<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\ProductRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`product`')]
class Product
{
    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['default'])]
    private readonly string $id;

    #[ORM\Column(length: 200)]
    #[Groups(['default'])]
    private string $name;

    #[ORM\Column(length: 200)]
    #[Groups(['default'])]
    private float $price;

    #[ORM\Column]
    #[Groups(['default'])]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column]
    #[Groups(['default'])]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        string $name,
        float $price
    ) {
        $this->id = (string) Uuid::v1();
        $this->name = $name;
        $this->price = $price;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function refreshUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
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
