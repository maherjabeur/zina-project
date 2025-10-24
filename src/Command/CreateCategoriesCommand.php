<?php
// src/Command/CreateCategoriesCommand.php
namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCategoriesCommand extends Command
{
    protected static $defaultName = 'app:create-categories';
    
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Crée les catégories par défaut');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categories = [
            [
                'name' => 'Robes',
                'slug' => 'robes',
                'description' => 'Collection de robes élégantes pour toutes les occasions',
                'color' => '#e83e8c',
                'icon' => 'fas fa-dress',
                'position' => 1
            ],
            [
                'name' => 'Jupes',
                'slug' => 'jupes',
                'description' => 'Jupes tendance et confortables',
                'color' => '#6f42c1',
                'icon' => 'fas fa-skirt',
                'position' => 2
            ],
            [
                'name' => 'Pantalons',
                'slug' => 'pantalons',
                'description' => 'Pantalons stylés pour un look moderne',
                'color' => '#20c997',
                'icon' => 'fas fa-vest',
                'position' => 3
            ],
            [
                'name' => 'Hauts',
                'slug' => 'hauts',
                'description' => 'Tops, t-shirts et chemisiers fashion',
                'color' => '#fd7e14',
                'icon' => 'fas fa-tshirt',
                'position' => 4
            ],
            [
                'name' => 'Vestes',
                'slug' => 'vestes',
                'description' => 'Vestes et blazers pour compléter votre tenue',
                'color' => '#6c757d',
                'icon' => 'fas fa-jacket',
                'position' => 5
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setSlug($categoryData['slug']);
            $category->setDescription($categoryData['description']);
            $category->setColor($categoryData['color']);
            $category->setIcon($categoryData['icon']);
            $category->setPosition($categoryData['position']);
            
            $this->entityManager->persist($category);
            $output->writeln('Catégorie créée: ' . $categoryData['name']);
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('Toutes les catégories ont été créées avec succès!');

        return Command::SUCCESS;
    }
}