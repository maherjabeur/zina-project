<?php
// src/Entity/OrderItem.php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderItemRepository;
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $unitPrice = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    private ?float $originalPrice = null;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2, nullable: true)]
    private ?float $discount = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $promotionTitle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalPrice(): ?float
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?float $originalPrice): static
    {
        $this->originalPrice = $originalPrice;
        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): static
    {
        $this->discount = $discount;
        return $this;
    }

    public function getPromotionTitle(): ?string
    {
        return $this->promotionTitle;
    }

    public function setPromotionTitle(?string $promotionTitle): static
    {
        $this->promotionTitle = $promotionTitle;
        return $this;
    }

    /**
     * Calcule le montant de la réduction
     */
    public function getDiscountAmount(): float
    {
        if ($this->originalPrice && $this->unitPrice) {
            return $this->originalPrice - $this->unitPrice;
        }
        return 0;
    }

    /**
     * Vérifie si cet item a une promotion
     */
    public function hasDiscount(): bool
    {
        return $this->discount > 0 && $this->originalPrice > $this->unitPrice;
    }

    /**
     * Calcule le sous-total avant réduction
     */
    public function getOriginalSubtotal(): float
    {
        if ($this->originalPrice) {
            return $this->originalPrice * $this->quantity;
        }
        return $this->unitPrice * $this->quantity;
    }
    
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        $this->calculateTotal();
        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        $this->calculateTotal();
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    private function calculateTotal(): void
    {
        if ($this->unitPrice && $this->quantity) {
            $this->total = bcmul($this->unitPrice, (string) $this->quantity, 2);
        }
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }
}