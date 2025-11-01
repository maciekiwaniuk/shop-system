<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(
    name: 'order_search_idx',
    columns: ['id'],
)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Groups(['default'])]
    private readonly string $id;

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['default'])]
    private readonly Client $client;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'order_id', nullable: false)]
    #[Groups(['default'])]
    private Collection $ordersProducts;

    /**
     * @var Collection<int, OrderStatusUpdate>
     */
    #[ORM\OneToMany(targetEntity: OrderStatusUpdate::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'order_id', nullable: false)]
    #[Groups(['default'])]
    private Collection $ordersStatusUpdates;

    #[ORM\Column(name: 'completed_at', nullable: true)]
    #[Groups(['default'])]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column(name: 'created_at', length: 255)]
    #[Groups(['default'])]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        Client $client,
    ) {
        $this->id = (string) Uuid::v1();
        $this->client = $client;
        $this->ordersProducts = new ArrayCollection();
        $this->ordersStatusUpdates = new ArrayCollection();
        $this->ordersStatusUpdates->add(new OrderStatusUpdate($this));
        $this->completedAt = null;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
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

    public function addProduct(
        Product $product,
        int $productQuantity,
        float $productPricePerPiece,
    ): self {
        $this->ordersProducts->add(
            new OrderProduct(
                order: $this,
                product: $product,
                productQuantity: $productQuantity,
                productPricePerPiece: $productPricePerPiece,
            ),
        );
        return $this;
    }

    public function updateStatus(OrderStatus $orderStatusUpdate): self
    {
        $this->ordersStatusUpdates->add(
            new OrderStatusUpdate(
                order: $this,
                status: $orderStatusUpdate,
            ),
        );
        if (in_array($orderStatusUpdate->value, [OrderStatus::COMPLETED->value, OrderStatus::CANCELED->value])) {
            $this->setCompletedAt(new DateTimeImmutable());
        }
        return $this;
    }

    public function getCurrentStatus(): string
    {
        return $this->ordersStatusUpdates->last()->getStatus();
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

    public function getTotalCost(): float
    {
        $totalCost = 0;
        foreach ($this->ordersProducts as $orderProduct) {
            $totalCost += $orderProduct->getProductQuantity() * $orderProduct->getProductPricePerPiece();
        }
        return $totalCost;
    }
}
