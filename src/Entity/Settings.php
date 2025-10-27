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

    // Getter et Setter
    public function getShippingFee(): ?float
    {
        return $this->shippingFee;
    }

    public function setShippingFee(float $shippingFee): self
    {
        $this->shippingFee = $shippingFee;

        return $this;
    }
}
