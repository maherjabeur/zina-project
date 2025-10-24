<?php
// src/Command/CreateSizesCommand.php
namespace App\Command;

use App\Entity\Size;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSizesCommand extends Command
{
    protected static $defaultName = 'app:create-sizes';
    
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Crée les tailles par défaut');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sizes = [
            // Vêtements
            ['name' => 'Extra Small', 'code' => 'XS', 'type' => 'clothing', 'position' => 1],
            ['name' => 'Small', 'code' => 'S', 'type' => 'clothing', 'position' => 2],
            ['name' => 'Medium', 'code' => 'M', 'type' => 'clothing', 'position' => 3],
            ['name' => 'Large', 'code' => 'L', 'type' => 'clothing', 'position' => 4],
            ['name' => 'Extra Large', 'code' => 'XL', 'type' => 'clothing', 'position' => 5],
            ['name' => 'Double Extra Large', 'code' => 'XXL', 'type' => 'clothing', 'position' => 6],
            
            // Chaussures
            ['name' => '36', 'code' => '36', 'type' => 'shoes', 'position' => 10],
            ['name' => '37', 'code' => '37', 'type' => 'shoes', 'position' => 11],
            ['name' => '38', 'code' => '38', 'type' => 'shoes', 'position' => 12],
            ['name' => '39', 'code' => '39', 'type' => 'shoes', 'position' => 13],
            ['name' => '40', 'code' => '40', 'type' => 'shoes', 'position' => 14],
            ['name' => '41', 'code' => '41', 'type' => 'shoes', 'position' => 15],
            ['name' => '42', 'code' => '42', 'type' => 'shoes', 'position' => 16],
            
            // Accessoires
            ['name' => 'Unique', 'code' => 'UNIQUE', 'type' => 'accessories', 'position' => 20],
            ['name' => 'Standard', 'code' => 'STANDARD', 'type' => 'accessories', 'position' => 21],
        ];

        foreach ($sizes as $sizeData) {
            $size = new Size();
            $size->setName($sizeData['name']);
            $size->setCode($sizeData['code']);
            $size->setType($sizeData['type']);
            $size->setPosition($sizeData['position']);
            
            $this->entityManager->persist($size);
            $output->writeln('Taille créée: ' . $sizeData['name'] . ' (' . $sizeData['code'] . ')');
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('Toutes les tailles ont été créées avec succès!');

        return Command::SUCCESS;
    }
}

  