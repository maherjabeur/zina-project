<?php
// src/Entity/Product.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ProductRepository;
use App\Entity\Category;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(targetEntity: Size::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Size $size = null;

    #[ORM\Column(length: 50)]
    private ?string $color = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, cascade: ['persist'])]
    private Collection $images;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Getters et Setters complets
    public function getId(): ?int { return $this->id; }
    
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    
    public function getPrice(): ?string { return $this->price; }
    public function setPrice(string $price): self { $this->price = $price; return $this; }
    
    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = $quantity; return $this; }
    
    public function getSize(): ?Size { return $this->size; }
    public function setSize(?Size $size): self { 
        $this->size = $size;
        return $this;
    }
    
    public function getColor(): ?string { return $this->color; }
    public function setColor(string $color): self { $this->color = $color; return $this; }
    
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { 
        $this->category = $category;
        return $this;
    }
    
    // Méthodes pour isActive
    public function getIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }
    
    // Méthode alternative (convention Symfony)
    public function isActive(): bool { return $this->isActive; }
    
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection { return $this->images; }
    
    public function addImage(ProductImage $image): self {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }
        return $this;
    }
    
    public function removeImage(ProductImage $image): self {
        if ($this->images->removeElement($image)) {
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }
        return $this;
    }
}