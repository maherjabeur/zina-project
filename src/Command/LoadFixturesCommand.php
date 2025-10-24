<?php
// src/Command/LoadFixturesCommand.php
namespace App\Command;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoadFixturesCommand extends Command
{
    protected static $defaultName = 'app:load-fixtures';
    
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Charge les données de test')
            ->setHelp('Cette commande charge des données de test pour l\'application');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Début du chargement des fixtures...');

        // Créer un admin
        $admin = new User();
        $admin->setEmail('admin@boutique.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('System');
        $admin->setIsAdmin(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        
        $this->entityManager->persist($admin);
        $output->writeln('Admin créé: admin@boutique.com / admin123');

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@boutique.com');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setIsAdmin(false);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        
        $this->entityManager->persist($user);
        $output->writeln('Utilisateur créé: user@boutique.com / user123');

        // Créer des produits de test
        $productsData = [
            ['Robe d\'été fleurie', 'Belle robe légère pour l\'été en coton bio', 49.99, 'M', 'Rose', 'Robe', 25],
            ['Jupe plissée', 'Jupe élégante pour le bureau en tissu stretch', 39.99, 'S', 'Noir', 'Jupe', 15],
            ['Top en soie', 'Haut en soie naturelle, confortable et élégant', 29.99, 'L', 'Blanc', 'Haut', 30],
            ['Pantalon wide-leg', 'Pantalon tendance coupe large en lin', 59.99, 'M', 'Beige', 'Pantalon', 20],
            ['Robe cocktail', 'Robe soirée avec dentelle et perles', 79.99, 'S', 'Bordeaux', 'Robe', 10],
            ['Blazer structuré', 'Blazer femme business avec épaulettes', 89.99, 'L', 'Marine', 'Veste', 12],
            ['Jean slim', 'Jean stretch confortable coupe slim', 45.99, '38', 'Bleu', 'Jean', 18],
            ['Chemisier satin', 'Chemisier luxueux en satin de soie', 35.99, 'M', 'Ivoire', 'Chemisier', 22],
        ];

        foreach ($productsData as $data) {
            $product = new Product();
            $product->setName($data[0]);
            $product->setDescription($data[1]);
            $product->setPrice($data[2]);
            $product->setSize($data[3]);
            $product->setColor($data[4]);
            $product->setCategory(null);
            $product->setQuantity($data[6]);
            
            $this->entityManager->persist($product);
            $output->writeln('Produit créé: ' . $data[0]);
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('Fixtures chargées avec succès!');
        $output->writeln('Comptes créés:');
        $output->writeln('- Admin: admin@boutique.com / admin123');
        $output->writeln('- User: user@boutique.com / user123');
        $output->writeln('Produits: ' . count($productsData) . ' produits créés');

        return Command::SUCCESS;
    }
}