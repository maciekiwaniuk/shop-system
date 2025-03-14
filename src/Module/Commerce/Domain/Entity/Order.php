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
    public Collection $ordersProducts {
        get => $this->ordersProducts;
    }

    /**
     * @var Collection<int, OrderStatusUpdate>
     */
    #[ORM\OneToMany(targetEntity: OrderStatusUpdate::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'order_id', nullable: false)]
    #[Groups(['default'])]
    public Collection $ordersStatusUpdates {
        get => $this->ordersStatusUpdates;
    }

    #[ORM\Column(nullable: true)]
    #[Groups(['default'])]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column(length: 255)]
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
        if ($orderStatusUpdate->value === OrderStatus::DELIVERED->value) {
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
}
