<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: '`order_product`')]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(length: 255)]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'ordersProducts')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['order'])]
    private readonly Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['default'])]
    private readonly Product $product;

    #[ORM\Column(name: 'product_quantity', length: 255)]
    #[Groups(['default'])]
    private readonly int $productQuantity;

    #[ORM\Column(name: 'product_price_per_piece', length: 255)]
    #[Groups(['default'])]
    private readonly float $productPricePerPiece;

    public function __construct(
        Order $order,
        Product $product,
        int $productQuantity,
        float $productPricePerPiece,
    ) {
        $this->order = $order;
        $this->product = $product;
        $this->productQuantity = $productQuantity;
        $this->productPricePerPiece = $productPricePerPiece;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getProductQuantity(): int
    {
        return $this->productQuantity;
    }

    public function getProductPricePerPiece(): float
    {
        return $this->productPricePerPiece;
    }
}
