<?php

namespace App\Repository;


use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    public function getCurrentSettings(): ?Settings
    {
        return $this->findOneBy([], ['id' => 'DESC']);
    }

    public function getOrCreateCurrentSettings(EntityManagerInterface $entityManager): Settings
    {
        $settings = $this->getCurrentSettings();

        if (!$settings) {
            $settings = new Settings();
            $entityManager->persist($settings);
            $entityManager->flush();
        }

        return $settings;
    }
}
