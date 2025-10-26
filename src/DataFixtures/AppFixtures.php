<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Size;
use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\ProductImage;
use App\Entity\SliderImage;
use App\Entity\Settings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadSettings($manager);
        $this->loadSizes($manager);
        $this->loadCategories($manager);
        $this->loadUsers($manager);
        $this->loadProducts($manager);
        $this->loadSliderImages($manager);
        $this->loadContacts($manager);
        
        $manager->flush();
    }

    private function loadSettings(ObjectManager $manager): void
    {
        $settings = new Settings();
        $settings->setShippingFee(3.99);

        $manager->persist($settings);
    }

    private function loadSizes(ObjectManager $manager): void
    {
        $clothingSizes = [
            ['32', '32'],
            ['34', '34'],
            ['36', '36'],
            ['38', '38'],
            ['40', '40'],
            ['42', '42'],
            ['44', '44'],
            ['46', '46'],
            ['48', '48']
        ];

        $lingerieSizes = [
            ['XS', 'Extra Small'],
            ['S', 'Small'],
            ['M', 'Medium'],
            ['L', 'Large'],
            ['XL', 'Extra Large']
        ];

        $shoeSizes = [
            ['35', '35'],
            ['36', '36'],
            ['37', '37'],
            ['38', '38'],
            ['39', '39'],
            ['40', '40'],
            ['41', '41'],
            ['42', '42']
        ];

        $position = 0;
        foreach ($clothingSizes as $sizeData) {
            $size = new Size();
            $size->setName($sizeData[1]);
            $size->setCode($sizeData[0]);
            $size->setType('clothing');
            $size->setPosition($position++);
            $size->setIsActive(true);

            $manager->persist($size);
            $this->addReference('size_clothing_' . $sizeData[0], $size);
        }

        $position = 0;
        foreach ($lingerieSizes as $sizeData) {
            $size = new Size();
            $size->setName($sizeData[1]);
            $size->setCode($sizeData[0]);
            $size->setType('lingerie');
            $size->setPosition($position++);
            $size->setIsActive(true);

            $manager->persist($size);
            $this->addReference('size_lingerie_' . $sizeData[0], $size);
        }

        $position = 0;
        foreach ($shoeSizes as $sizeData) {
            $size = new Size();
            $size->setName($sizeData[1]);
            $size->setCode($sizeData[0]);
            $size->setType('shoes');
            $size->setPosition($position++);
            $size->setIsActive(true);

            $manager->persist($size);
            $this->addReference('size_shoe_' . $sizeData[0], $size);
        }
    }

    private function loadCategories(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => 'Robes',
                'slug' => 'robes',
                'description' => 'Collection de robes élégantes pour toutes les occasions',
                'color' => '#e91e63',
                'icon' => 'dress',
                'position' => 1
            ],
            [
                'name' => 'Hauts & T-shirts',
                'slug' => 'hauts-tshirts',
                'description' => 'Tops, t-shirts et chemisiers tendance',
                'color' => '#9c27b0',
                'icon' => 'shirt',
                'position' => 2
            ],
            [
                'name' => 'Bas',
                'slug' => 'bas',
                'description' => 'Jupes, shorts et pantalons féminins',
                'color' => '#673ab7',
                'icon' => 'pants',
                'position' => 3
            ],
            [
                'name' => 'Ensembles',
                'slug' => 'ensembles',
                'description' => 'Tenues complètes et coordonnés',
                'color' => '#2196f3',
                'icon' => 'outfit',
                'position' => 4
            ],
            [
                'name' => 'Lingerie',
                'slug' => 'lingerie',
                'description' => 'Sous-vêtements et lingerie fine',
                'color' => '#ff4081',
                'icon' => 'lingerie',
                'position' => 5
            ],
            [
                'name' => 'Accessoires',
                'slug' => 'accessoires',
                'description' => 'Sacs, bijoux et accessoires mode',
                'color' => '#ff9800',
                'icon' => 'accessories',
                'position' => 6
            ],
            [
                'name' => 'Chaussures',
                'slug' => 'chaussures',
                'description' => 'Chaussures femme pour compléter votre look',
                'color' => '#795548',
                'icon' => 'shoes',
                'position' => 7
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
            $category->setIsActive(true);

            $manager->persist($category);
            $this->addReference('category_' . $categoryData['slug'], $category);
        }
    }

    private function loadUsers(ObjectManager $manager): void
    {
        // Admin user
        $admin = new User();
        $admin->setEmail('admin@boutique-femme.com');
        $admin->setFirstName('Sophie');
        $admin->setLastName('Martin');
        $admin->setIsAdmin(true);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Regular users (all women)
        $users = [
            ['marie.dubois@email.com', 'Marie', 'Dubois', 'user123'],
            ['julie.leroy@email.com', 'Julie', 'Leroy', 'user123'],
            ['sarah.bernard@email.com', 'Sarah', 'Bernard', 'user123'],
            ['laura.petit@email.com', 'Laura', 'Petit', 'user123'],
            ['claire.moreau@email.com', 'Claire', 'Moreau', 'user123'],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData[0]);
            $user->setFirstName($userData[1]);
            $user->setLastName($userData[2]);
            $user->setIsAdmin(false);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData[3]));

            $manager->persist($user);
            $this->addReference('user_' . str_replace(['.', '@'], ['_', ''], $userData[0]), $user);
        }
    }

    private function loadProducts(ObjectManager $manager): void
    {
        $products = [
            // Robes
            [
                'name' => 'Robe Midi Élégante',
                'description' => 'Robe midi en crêpe avec encolure en V. Coupe fluide et tombante, parfaite pour les occasions spéciales.',
                'price' => '89.99',
                'quantity' => 25,
                'color' => 'Noir',
                'category' => 'category_robes',
                'sizes' => ['size_clothing_36', 'size_clothing_38', 'size_clothing_40', 'size_clothing_42'],
                'images' => ['robe-midi-noir-1.jpg', 'robe-midi-noir-2.jpg']
            ],
            [
                'name' => 'Robe d\'Été Florale',
                'description' => 'Robe légère à imprimé floral, manches ballon et ceinture nouée. Idéale pour les journées ensoleillées.',
                'price' => '49.99',
                'quantity' => 35,
                'color' => 'Multicolore',
                'category' => 'category_robes',
                'sizes' => ['size_clothing_34', 'size_clothing_36', 'size_clothing_38', 'size_clothing_40'],
                'images' => ['robe-florale-1.jpg', 'robe-florale-2.jpg']
            ],
            [
                'name' => 'Robe Cocktail Satin',
                'description' => 'Robe courte en satin avec dos nu. Élégante et sophistiquée pour vos soirées.',
                'price' => '75.99',
                'quantity' => 20,
                'color' => 'Bordeaux',
                'category' => 'category_robes',
                'sizes' => ['size_clothing_36', 'size_clothing_38', 'size_clothing_40'],
                'images' => ['robe-cocktail-1.jpg', 'robe-cocktail-2.jpg']
            ],

            // Hauts
            [
                'name' => 'Chemisier en Soie',
                'description' => 'Chemisier luxueux en soie naturelle, col chemisier et manches longues. Élégant et intemporel.',
                'price' => '69.99',
                'quantity' => 30,
                'color' => 'Blanc',
                'category' => 'category_hauts-tshirts',
                'sizes' => ['size_clothing_36', 'size_clothing_38', 'size_clothing_40', 'size_clothing_42'],
                'images' => ['chemisier-soie-1.jpg', 'chemisier-soie-2.jpg']
            ],
            [
                'name' => 'T-shirt Col V Basique',
                'description' => 'T-shirt en coton bio, col V et coupe ajustée. Essentiel de toute garde-robe.',
                'price' => '19.99',
                'quantity' => 50,
                'color' => 'Gris',
                'category' => 'category_hauts-tshirts',
                'sizes' => ['size_clothing_34', 'size_clothing_36', 'size_clothing_38', 'size_clothing_40', 'size_clothing_42'],
                'images' => ['tshirt-colv-1.jpg', 'tshirt-colv-2.jpg']
            ],
            [
                'name' => 'Top Manches Ballon',
                'description' => 'Top tendance avec manches ballon et encolure carrée. Parfait pour un look moderne.',
                'price' => '34.99',
                'quantity' => 40,
                'color' => 'Rose poudré',
                'category' => 'category_hauts-tshirts',
                'sizes' => ['size_clothing_34', 'size_clothing_36', 'size_clothing_38'],
                'images' => ['top-balloon-1.jpg', 'top-balloon-2.jpg']
            ],

            // Bas
            [
                'name' => 'Jean Slim Taille Haute',
                'description' => 'Jean slim stretch taille haute, coupe flatteuse et confortable. Délavage moyen.',
                'price' => '59.99',
                'quantity' => 45,
                'color' => 'Bleu',
                'category' => 'category_bas',
                'sizes' => ['size_clothing_36', 'size_clothing_38', 'size_clothing_40', 'size_clothing_42'],
                'images' => ['jean-slim-1.jpg', 'jean-slim-2.jpg']
            ],
            [
                'name' => 'Jupe Midi Plissée',
                'description' => 'Jupe midi plissée en tissu fluide. Élégante et facile à porter au quotidien.',
                'price' => '45.99',
                'quantity' => 28,
                'color' => 'Beige',
                'category' => 'category_bas',
                'sizes' => ['size_clothing_36', 'size_clothing_38', 'size_clothing_40'],
                'images' => ['jupe-plissee-1.jpg', 'jupe-plissee-2.jpg']
            ],
            [
                'name' => 'Short en Lin',
                'description' => 'Short court en lin naturel, coupe droite et poche passepoilée. Idéal pour l\'été.',
                'price' => '39.99',
                'quantity' => 32,
                'color' => 'Écru',
                'category' => 'category_bas',
                'sizes' => ['size_clothing_36', 'size_clothing_38', 'size_clothing_40'],
                'images' => ['short-lin-1.jpg', 'short-lin-2.jpg']
            ],

            // Lingerie
            [
                'name' => 'Soutien-gorge Dentelle',
                'description' => 'Soutien-gorge push-up en dentelle fine, rembourrage amovible. Confort et séduction.',
                'price' => '29.99',
                'quantity' => 60,
                'color' => 'Noir',
                'category' => 'category_lingerie',
                'sizes' => ['size_lingerie_S', 'size_lingerie_M', 'size_lingerie_L'],
                'images' => ['soutien-dentelle-1.jpg', 'soutien-dentelle-2.jpg']
            ],
            [
                'name' => 'Culotte Boxer Dentelle',
                'description' => 'Culotte boxer en dentelle élastique, confortable et élégante.',
                'price' => '15.99',
                'quantity' => 80,
                'color' => 'Blanc',
                'category' => 'category_lingerie',
                'sizes' => ['size_lingerie_XS', 'size_lingerie_S', 'size_lingerie_M'],
                'images' => ['culotte-dentelle-1.jpg', 'culotte-dentelle-2.jpg']
            ],

            // Chaussures
            [
                'name' => 'Escarpins Talons 8cm',
                'description' => 'Escarpins en cuir verni avec talon aiguille 8cm. Élégance et sophistication.',
                'price' => '79.99',
                'quantity' => 25,
                'color' => 'Noir',
                'category' => 'category_chaussures',
                'sizes' => ['size_shoe_37', 'size_shoe_38', 'size_shoe_39', 'size_shoe_40'],
                'images' => ['escarpins-1.jpg', 'escarpins-2.jpg']
            ],
            [
                'name' => 'Baskets Cuir Blanc',
                'description' => 'Baskets en cuir véritable, semelle confort et design épuré. Polyvalentes et stylées.',
                'price' => '89.99',
                'quantity' => 35,
                'color' => 'Blanc',
                'category' => 'category_chaussures',
                'sizes' => ['size_shoe_36', 'size_shoe_37', 'size_shoe_38', 'size_shoe_39'],
                'images' => ['baskets-blanc-1.jpg', 'baskets-blanc-2.jpg']
            ],

            // Accessoires
            [
                'name' => 'Sac Main Chaîne Dorée',
                'description' => 'Sac à main avec chaîne dorée, compartiment principal et poche intérieure.',
                'price' => '65.99',
                'quantity' => 20,
                'color' => 'Camel',
                'category' => 'category_accessoires',
                'sizes' => ['size_clothing_UNI'],
                'images' => ['sac-chaine-1.jpg', 'sac-chaine-2.jpg']
            ],
            [
                'name' => 'Collier Perles de Culture',
                'description' => 'Collier élégant avec perles de culture et fermoir en or. Intemporel et raffiné.',
                'price' => '45.99',
                'quantity' => 40,
                'color' => 'Blanc',
                'category' => 'category_accessoires',
                'sizes' => ['size_clothing_UNI'],
                'images' => ['collier-perles-1.jpg', 'collier-perles-2.jpg']
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setPrice($productData['price']);
            $product->setQuantity($productData['quantity']);
            $product->setColor($productData['color']);
            
            // CORRECTION : Utilisation correcte de getReference avec la classe
            $category = $manager->getRepository(Category::class)->findOneBy(['slug' => str_replace('category_', '', $productData['category'])]);
            if ($category) {
                $product->setCategory($category);
            }

            $product->setIsActive(true);

            // Ajouter les tailles
            foreach ($productData['sizes'] as $sizeRef) {
                try {
                    $size = $this->getReference($sizeRef,"Size");
                    $product->addSize($size);
                } catch (\Exception $e) {
                    // Ignorer les références non trouvées
                    continue;
                }
            }

            // Ajouter des images fictives
            foreach ($productData['images'] as $index => $filename) {
                $image = new ProductImage();
                $image->setFilename($filename);
                $image->setPosition($index);
                $product->addImage($image);
                $manager->persist($image);
            }

            $manager->persist($product);
        }
    }

    private function loadSliderImages(ObjectManager $manager): void
    {
        $sliderImages = [
            [
                'filename' => 'slide-femme-1.jpg',
                'title' => 'Nouvelle Collection Printemps',
                'description' => 'Découvrez les nouvelles tendances mode femme de la saison',
                'buttonText' => 'Découvrir',
                'buttonUrl' => '/categories',
                'position' => 1
            ],
            [
                'filename' => 'slide-femme-2.jpg',
                'title' => 'Robes d\'Été',
                'description' => 'Des robes légères et élégantes pour briller cet été',
                'buttonText' => 'Voir les robes',
                'buttonUrl' => '/categorie/robes',
                'position' => 2
            ],
            [
                'filename' => 'slide-femme-3.jpg',
                'title' => 'Soldes Exclusives',
                'description' => 'Jusqu\'à -50% sur une sélection d\'articles',
                'buttonText' => 'Profiter des soldes',
                'buttonUrl' => '/promotions',
                'position' => 3
            ],
            [
                'filename' => 'slide-femme-4.jpg',
                'title' => 'Livraison Offerte',
                'description' => 'Livraison gratuite dès 60€ d\'achat',
                'buttonText' => 'En savoir plus',
                'buttonUrl' => '/livraison',
                'position' => 4
            ]
        ];

        foreach ($sliderImages as $sliderData) {
            $slider = new SliderImage();
            $slider->setFilename($sliderData['filename']);
            $slider->setTitle($sliderData['title']);
            $slider->setDescription($sliderData['description']);
            $slider->setButtonText($sliderData['buttonText']);
            $slider->setButtonUrl($sliderData['buttonUrl']);
            $slider->setPosition($sliderData['position']);
            $slider->setIsActive(true);

            $manager->persist($slider);
        }
    }

    private function loadContacts(ObjectManager $manager): void
    {
        $contacts = [
            [
                'name' => 'Marie Dubois',
                'email' => 'marie.dubois@email.com',
                'phone' => '0123456789',
                'message' => 'Bonjour, je souhaiterais connaître les délais de livraison pour une robe commandée en taille 38.',
                'isRead' => true
            ],
            [
                'name' => 'Julie Martin',
                'email' => 'julie.martin@email.com',
                'phone' => '0654321098',
                'message' => 'J\'ai reçu un chemisier avec un défaut, comment puis-je l\'échanger ?',
                'isRead' => false
            ],
            [
                'name' => 'Sophie Leroy',
                'email' => 'sophie.leroy@email.com',
                'phone' => '0789456123',
                'message' => 'Proposez-vous un service de retouche pour les vêtements ?',
                'isRead' => true
            ],
            [
                'name' => 'Claire Petit',
                'email' => 'claire.petit@email.com',
                'phone' => null,
                'message' => 'Quand sortira votre nouvelle collection de lingerie ?',
                'isRead' => false
            ]
        ];

        foreach ($contacts as $contactData) {
            $contact = new Contact();
            $contact->setName($contactData['name']);
            $contact->setEmail($contactData['email']);
            $contact->setPhone($contactData['phone']);
            $contact->setMessage($contactData['message']);
            $contact->setIsRead($contactData['isRead']);
            $contact->setCreatedAt(new \DateTime('-'.rand(1, 30).' days'));

            $manager->persist($contact);
        }
    }
}