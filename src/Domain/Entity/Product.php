<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\ProductRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV1;

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
    private string $slug;

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
        $uuid = Uuid::v1();
        $this->id = (string) $uuid;
        $this->name = $name;
        $this->slug = $this->generateSlug($name, $uuid);
        $this->price = $price;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function refreshUpdatedAtValue(): void
    {
        $this->slug = $this->generateSlug(
            $this->name,
            UuidV1::fromString($this->id)
        );
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    private function generateSlug(string $name, UuidV1 $uuid): string
    {
        return (new AsciiSlugger())->slug($name) . '-' . substr((string) $uuid, 0, 8);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
}
