<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Enum\OrderStatus;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: '`order_status_update`')]
class OrderStatusUpdate
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Groups(['default'])]
    private readonly string $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'ordersStatusUpdates')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['order'])]
    private readonly Order $order;

    #[ORM\Column(length: 200)]
    #[Groups(['default'])]
    private readonly OrderStatus $status;

    #[ORM\Column(length: 255)]
    #[Groups(['default'])]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        Order $order,
        OrderStatus $status = OrderStatus::WAITING_FOR_PAYMENT,
    ) {
        $this->id = (string) Uuid::v1();
        $this->order = $order;
        $this->status = $status;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
