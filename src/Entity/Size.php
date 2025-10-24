<?php
// src/Entity/Size.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\SizeRepository;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
class Size
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null; // 'clothing', 'shoes', 'accessories'

    #[ORM\Column(type: 'integer')]
    private int $position = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt;

    #[ORM\OneToMany(mappedBy: 'size', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { 
        $this->name = $name;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): self { 
        $this->code = $code;
        return $this;
    }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { 
        $this->type = $type;
        return $this;
    }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): self { 
        $this->position = $position;
        return $this;
    }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { 
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { 
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self { 
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection { return $this->products; }
    public function addProduct(Product $product): self {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSize($this);
        }
        return $this;
    }
    public function removeProduct(Product $product): self {
        if ($this->products->removeElement($product)) {
            if ($product->getSize() === $this) {
                $product->setSize(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}