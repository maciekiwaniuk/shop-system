<?php

declare(strict_types=1);

namespace App\Module\Order\Domain\Entity;

use App\Module\Order\Domain\Enum\OrderStatus;
use App\Module\Order\Domain\Repository\OrderRepositoryInterface;
use App\Module\Product\Domain\Entity\Product;
use App\Module\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['default'])]
    private readonly string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['default'])]
    private readonly User $user;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(mappedBy: 'ordersProducts', targetEntity: OrderProduct::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'order_id', nullable: false)]
    #[Groups(['default'])]
    private Collection $ordersProducts;

    /**
     * @var Collection<int, OrderStatusUpdate>
     */
    #[ORM\OneToMany(mappedBy: 'ordersStatusUpdates', targetEntity: OrderStatusUpdate::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'order_id', nullable: false)]
    #[Groups(['default'])]
    private Collection $ordersStatusUpdates;

    #[ORM\Column]
    #[Groups(['default'])]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column]
    #[Groups(['default'])]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        User $user
    ) {
        $this->id = (string) Uuid::v1();
        $this->user = $user;
        $this->ordersProducts = new ArrayCollection();
        $this->ordersStatusUpdates = (new ArrayCollection());
        $this->ordersStatusUpdates->add(new OrderStatusUpdate($this));
        $this->completedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrdersProducts(): Collection
    {
        return $this->ordersProducts;
    }

    /**
     * @return Collection<int, OrderStatusUpdate>
     */
    public function getOrdersStatusUpdates(): Collection
    {
        return $this->ordersStatusUpdates;
    }

    public function createAndAddOrderProduct(
        Product $product,
        int $productQuantity,
        float $productPricePerPiece
    ): self {
        $this->ordersProducts->add(
            new OrderProduct(
                order: $this,
                product: $product,
                productQuantity: $productQuantity,
                productPricePerPiece: $productPricePerPiece
            )
        );
        return $this;
    }

    public function updateStatus(OrderStatus $orderStatusUpdate): self
    {
        $this->ordersStatusUpdates->add(
            new OrderStatusUpdate(
                order: $this,
                status: $orderStatusUpdate
            )
        );
        return $this;
    }

    public function setCompletedAt(DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
