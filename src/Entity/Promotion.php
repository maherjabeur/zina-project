<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PromotionRepository;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
#[ORM\Index(name: 'idx_promotion_product_active_discount', columns: ['product_id', 'is_active', 'discount'])]
#[ORM\Index(name: 'idx_promotion_dates', columns: ['start_date', 'end_date'])]
class Promotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $titleAr = null;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $descriptionAr = null;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2)]
    private string $discount;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "promotions")]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private Product $product;

    #[ORM\Column(type: "boolean")]
    private bool $isActive = true;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitleAr(): ?string
    {
        return $this->titleAr;
    }

    public function setTitleAr(?string $titleAr): self
    {
        $this->titleAr = $titleAr;
        return $this;
    }

    public function getLocalizedTitle(string $locale): string
    {
        return $locale === 'ar' && $this->titleAr ? $this->titleAr : $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescriptionAr(): ?string
    {
        return $this->descriptionAr;
    }

    public function setDescriptionAr(?string $descriptionAr): self
    {
        $this->descriptionAr = $descriptionAr;
        return $this;
    }

    public function getLocalizedDescription(string $locale): string
    {
        return $locale === 'ar' && $this->descriptionAr ? $this->descriptionAr : $this->description;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): self
    {
        $this->discount = number_format($discount, 2, '.', '');
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Vérifie si la promotion est actuellement valide
     */
    public function isValid(): bool
    {
        if (!$this->isActive) {
            return false;
        }

        $now = new \DateTime();

        if ($this->startDate && $this->startDate > $now) {
            return false;
        }

        if ($this->endDate && $this->endDate < $now) {
            return false;
        }

        return true;
    }

    /**
     * Calcule le prix après réduction
     */
    public function calculateDiscountedPrice(float $originalPrice): float
    {
        return $originalPrice * (1 - ($this->discount / 100));
    }

    /**
     * Retourne le montant de la réduction
     */
    public function getDiscountAmount(float $originalPrice): float
    {
        return $originalPrice * ($this->discount / 100);
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->title . ' (' . $this->discount . '%)';
    }
}
