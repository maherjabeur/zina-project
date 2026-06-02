<?php
namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: "decimal", scale: 2)]
    private $shippingFee = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitle = 'Bella Couture - Mode feminine elegante';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $seoDescription = 'Boutique de mode feminine a Sousse: vetements elegants, collections tendance, tailles et couleurs au choix avec livraison en Tunisie.';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $seoKeywords = 'mode feminine, boutique femme, vetements femme, Bella Couture, Sousse, Tunisie';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoImage = 'logo/logo.webp';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoAuthor = 'Bella Couture';

    #[ORM\Column(type: 'boolean')]
    private bool $seoIndexingEnabled = true;

    public function getShippingFee(): ?float
    {
        return $this->shippingFee;
    }

    public function setShippingFee(float $shippingFee): self
    {
        $this->shippingFee = $shippingFee;

        return $this;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): self
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(?string $seoDescription): self
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    public function getSeoKeywords(): ?string
    {
        return $this->seoKeywords;
    }

    public function setSeoKeywords(?string $seoKeywords): self
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    public function getSeoImage(): ?string
    {
        return $this->seoImage;
    }

    public function setSeoImage(?string $seoImage): self
    {
        $this->seoImage = $seoImage;

        return $this;
    }

    public function getSeoAuthor(): ?string
    {
        return $this->seoAuthor;
    }

    public function setSeoAuthor(?string $seoAuthor): self
    {
        $this->seoAuthor = $seoAuthor;

        return $this;
    }

    public function isSeoIndexingEnabled(): bool
    {
        return $this->seoIndexingEnabled;
    }

    public function setSeoIndexingEnabled(bool $seoIndexingEnabled): self
    {
        $this->seoIndexingEnabled = $seoIndexingEnabled;

        return $this;
    }
}
