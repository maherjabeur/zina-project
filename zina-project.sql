-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 17 juin 2026 à 10:50
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `zina-project`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ar` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_64C19C1989D9B62` (`slug`),
  KEY `idx_category_active_position` (`is_active`,`position`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `slug`, `description`, `color`, `icon`, `position`, `is_active`, `created_at`, `updated_at`, `name_ar`, `description_ar`) VALUES
(1, 'Robes', 'robes', 'Collection de robes élégantes pour toutes les occasions', '#e91e63', 'dress', 1, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'فساتين', 'تشكيلة فساتين أنيقة لكل المناسبات'),
(2, 'Hauts & T-shirts', 'hauts-tshirts', 'Tops, t-shirts et chemisiers tendance', '#9c27b0', 'shirt', 2, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'قمصان وتيشيرتات', 'توبات وتيشيرتات وقمصان عصرية'),
(3, 'Bas', 'bas', 'Jupes, shorts et pantalons féminins', '#673ab7', 'pants', 3, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'قطع سفلية', 'تنانير وشورتات وسراويل نسائية'),
(4, 'Ensembles', 'ensembles', 'Tenues complètes et coordonnés', '#2196f3', 'outfit', 4, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'أطقم', 'إطلالات كاملة ومنسقة'),
(5, 'Lingerie', 'lingerie', 'Sous-vêtements et lingerie fine', '#ff4081', 'lingerie', 5, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'لانجري', 'ملابس داخلية ولانجري راق'),
(6, 'Accessoires', 'accessoires', 'Sacs, bijoux et accessoires mode', '#ff9800', 'accessories', 6, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'إكسسوارات', 'حقائب ومجوهرات وإكسسوارات موضة'),
(7, 'Chaussures', 'chaussures', 'Chaussures femme pour compléter votre look', '#795548', 'shoes', 7, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'أحذية', 'أحذية نسائية لإكمال إطلالتك');

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260601174051', '2026-06-01 17:41:00', 307),
('DoctrineMigrations\\Version20260601192000', '2026-06-01 18:19:11', 38),
('DoctrineMigrations\\Version20260602113000', '2026-06-02 11:29:37', 163),
('DoctrineMigrations\\Version20260602124500', '2026-06-04 08:35:53', 516),
('DoctrineMigrations\\Version20260616093000', '2026-06-16 10:09:34', 500),
('DoctrineMigrations\\Version20260616103000', '2026-06-16 10:24:49', 117),
('DoctrineMigrations\\Version20260616194000', '2026-06-16 18:38:24', 103);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(1, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:39:\\\"Symfony\\\\Bridge\\\\Twig\\\\Mime\\\\TemplatedEmail\\\":5:{i:0;s:32:\\\"emails/new_order_admin.html.twig\\\";i:1;N;i:2;a:1:{s:5:\\\"order\\\";O:16:\\\"App\\\\Entity\\\\Order\\\":16:{s:20:\\\"\\0App\\\\Entity\\\\Order\\0id\\\";i:1;s:29:\\\"\\0App\\\\Entity\\\\Order\\0orderNumber\\\";s:17:\\\"CMD_6a1dc49d6af2b\\\";s:22:\\\"\\0App\\\\Entity\\\\Order\\0user\\\";N;s:31:\\\"\\0App\\\\Entity\\\\Order\\0customerEmail\\\";N;s:30:\\\"\\0App\\\\Entity\\\\Order\\0customerName\\\";s:19:\\\"Test Checkout Codex\\\";s:31:\\\"\\0App\\\\Entity\\\\Order\\0customerPhone\\\";s:8:\\\"12345678\\\";s:23:\\\"\\0App\\\\Entity\\\\Order\\0items\\\";O:33:\\\"Doctrine\\\\ORM\\\\PersistentCollection\\\":2:{s:13:\\\"\\0*\\0collection\\\";O:43:\\\"Doctrine\\\\Common\\\\Collections\\\\ArrayCollection\\\":1:{s:53:\\\"\\0Doctrine\\\\Common\\\\Collections\\\\ArrayCollection\\0elements\\\";a:0:{}}s:14:\\\"\\0*\\0initialized\\\";b:1;}s:23:\\\"\\0App\\\\Entity\\\\Order\\0total\\\";s:5:\\\"83.18\\\";s:24:\\\"\\0App\\\\Entity\\\\Order\\0status\\\";s:7:\\\"pending\\\";s:27:\\\"\\0App\\\\Entity\\\\Order\\0createdAt\\\";O:8:\\\"DateTime\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-06-01 17:42:53.438056\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:3:\\\"UTC\\\";}s:26:\\\"\\0App\\\\Entity\\\\Order\\0notified\\\";b:0;s:33:\\\"\\0App\\\\Entity\\\\Order\\0shippingAddress\\\";s:18:\\\"Adresse test Codex\\\";s:27:\\\"\\0App\\\\Entity\\\\Order\\0updatedAt\\\";O:8:\\\"DateTime\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-06-01 17:42:53.438058\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:3:\\\"UTC\\\";}s:26:\\\"\\0App\\\\Entity\\\\Order\\0discount\\\";s:5:\\\"10.80\\\";s:31:\\\"\\0App\\\\Entity\\\\Order\\0originalTotal\\\";s:5:\\\"89.99\\\";s:29:\\\"\\0App\\\\Entity\\\\Order\\0shippingFee\\\";s:4:\\\"3.99\\\";}}i:3;a:6:{i:0;N;i:1;N;i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:26:\\\"noreply@boutique-femme.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"admin@boutique-femme.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:37:\\\"Nouvelle commande - CMD_6a1dc49d6af2b\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}i:4;N;}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-06-01 17:42:53', '2026-06-01 17:42:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `notified` tinyint(1) NOT NULL,
  `shipping_address` longtext COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `original_total` decimal(10,2) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_F5299398A76ED395` (`user_id`),
  KEY `idx_order_status_created` (`status`,`created_at`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_order_new_created` (`is_new`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `user_id`, `order_number`, `customer_email`, `customer_name`, `customer_phone`, `total`, `status`, `created_at`, `notified`, `shipping_address`, `updated_at`, `discount`, `original_total`, `shipping_fee`, `is_new`) VALUES
(2, NULL, 'CMD_6a1eb481c229a', NULL, 'kkkkkkk', '25603131', 83.18, 'cancelled', '2026-06-02 10:46:25', 1, 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjj', '2026-06-04 08:33:11', 10.80, 89.99, 3.99, 0),
(3, NULL, 'CMD_6a1eb62be6d8d', NULL, 'aaaaaaaaa', '11111111', 83.18, 'delivered', '2026-06-02 10:53:31', 0, 'ssssssss', '2026-06-02 10:54:25', 10.80, 89.99, 3.99, 0),
(7, 7, 'CMD_6a219756bd349', NULL, 'qqqqqqqqqq', '22222222', 64.78, 'delivered', '2026-06-04 15:18:46', 0, 'qqqqqqqqqqqqq', '2026-06-04 15:19:09', 15.20, 75.99, 3.99, 0),
(8, NULL, 'CMD_6a319736e9b62', 'mjaber@altra-call.com', 'Maher Jabeur', '25603131', 73.98, 'delivered', '2026-06-16 18:34:30', 1, 'Bazma', '2026-06-16 18:56:39', 0.00, 69.99, 3.99, 0);

-- --------------------------------------------------------

--
-- Structure de la table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE IF NOT EXISTS `order_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT NULL,
  `promotion_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_52EA1F098D9F6D38` (`order_id`),
  KEY `IDX_52EA1F094584665A` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total`, `size`, `color`, `original_price`, `discount`, `promotion_title`) VALUES
(2, 2, 1, 1, 79.19, 79.19, '34', 'Noir', 89.99, 12.00, 'aaaaaaa'),
(3, 3, 1, 1, 79.19, 79.19, '34', 'Noir', 89.99, 12.00, 'aaaaaaa'),
(7, 7, 3, 1, 60.79, 60.79, '36', 'Bordeaux', 75.99, 20.00, 'qqqqqqqqqq'),
(8, 8, 4, 1, 69.99, 69.99, '36', 'Blanc', 69.99, 0.00, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ar` longtext COLLATE utf8mb4_unicode_ci,
  `color_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D34A04AD12469DE2` (`category_id`),
  KEY `idx_product_active_created` (`is_active`,`created_at`),
  KEY `idx_product_category_active` (`category_id`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `category_id`, `name`, `description`, `price`, `quantity`, `color`, `is_active`, `created_at`, `name_ar`, `description_ar`, `color_ar`) VALUES
(1, 1, 'Robe Midi Élégante', 'Robe midi en crêpe avec encolure en V. Coupe fluide et tombante, parfaite pour les occasions spéciales.', 89.99, 23, 'Noir, Bordeaux, Vert emeraude', 1, '2026-06-01 11:44:59', 'فستان ميدي أنيق', 'فستان ميدي من الكريب بياقة V. قصة ناعمة ومنسدلة، مثالي للمناسبات الخاصة.', 'أسود، بوردو، أخضر زمردي'),
(2, 1, 'Robe d\'Été Florale', 'Robe légère à imprimé floral, manches ballon et ceinture nouée. Idéale pour les journées ensoleillées.', 49.99, 35, 'Multicolore, Rose, Bleu ciel', 1, '2026-06-01 11:44:59', 'فستان صيفي مزهر', 'فستان خفيف بطبعة زهور، أكمام منفوخة وحزام معقود. مثالي للأيام المشمسة.', 'متعدد الألوان، وردي، أزرق سماوي'),
(3, 1, 'Robe Cocktail Satin', 'Robe courte en satin avec dos nu. Élégante et sophistiquée pour vos soirées.', 75.99, 19, 'Bordeaux, Noir, Champagne', 1, '2026-06-01 11:44:59', 'فستان كوكتيل ساتان', 'فستان قصير من الساتان بظهر مفتوح. أنيق وراقي لسهراتك.', 'بوردو، أسود، شامبانيا'),
(4, 2, 'Chemisier en Soie', 'Chemisier luxueux en soie naturelle, col chemisier et manches longues. Élégant et intemporel.', 69.99, 29, 'Blanc, Ivoire, Rose poudre', 1, '2026-06-01 11:44:59', 'قميص حرير', 'قميص فاخر من الحرير الطبيعي بياقة قميص وأكمام طويلة. أنيق وخالد.', 'أبيض، عاجي، وردي باهت'),
(5, 2, 'T-shirt Col V Basique', 'T-shirt en coton bio, col V et coupe ajustée. Essentiel de toute garde-robe.', 19.99, 50, 'Gris, Blanc, Noir', 1, '2026-06-01 11:44:59', 'تيشيرت أساسي بياقة V', 'تيشيرت من القطن العضوي بياقة V وقصة مضبوطة. قطعة أساسية في كل خزانة.', 'رمادي، أبيض، أسود'),
(6, NULL, 'Top Manches Ballon', 'Top tendance avec manches ballon et encolure carrée. Parfait pour un look moderne.', 34.99, 40, 'Rose poudre, Lilas, Beige', 1, '2026-06-01 11:44:59', 'توب بأكمام منفوخة', 'توب عصري بأكمام منفوخة وياقة مربعة. مثالي لإطلالة حديثة.', 'وردي باهت، ليلكي، بيج'),
(7, NULL, 'Jean Slim Taille Haute', 'Jean slim stretch taille haute, coupe flatteuse et confortable. Délavage moyen.', 59.99, 45, 'Bleu, Noir, Beige', 1, '2026-06-01 11:44:59', 'جينز سليم بخصر عال', 'جينز سليم مطاطي بخصر عال، قصة مريحة وتبرز القوام. لون أزرق متوسط.', 'أزرق، أسود، بيج'),
(8, NULL, 'Jupe Midi Plissée', 'Jupe midi plissée en tissu fluide. Élégante et facile à porter au quotidien.', 45.99, 28, 'Beige, Camel, Noir', 1, '2026-06-01 11:44:59', 'تنورة ميدي بكسرات', 'تنورة ميدي بكسرات من قماش ناعم. أنيقة وسهلة الارتداء يوميا.', 'بيج، جملي، أسود'),
(9, NULL, 'Short en Lin', 'Short court en lin naturel, coupe droite et poche passepoilée. Idéal pour l\'été.', 39.99, 32, 'Écru', 1, '2026-06-01 11:44:59', 'شورت كتان', 'شورت قصير من الكتان الطبيعي، قصة مستقيمة وجيب أنيق. مثالي للصيف.', 'عاجي'),
(10, NULL, 'Soutien-gorge Dentelle', 'Soutien-gorge push-up en dentelle fine, rembourrage amovible. Confort et séduction.', 29.99, 60, 'Noir', 1, '2026-06-01 11:44:59', 'حمالة صدر دانتيل', 'حمالة صدر بوش أب من دانتيل ناعم مع حشوة قابلة للإزالة. راحة وجاذبية.', 'أسود'),
(11, NULL, 'Culotte Boxer Dentelle', 'Culotte boxer en dentelle élastique, confortable et élégante.', 15.99, 80, 'Blanc', 1, '2026-06-01 11:44:59', 'سروال داخلي بوكسر دانتيل', 'سروال داخلي بوكسر من دانتيل مطاطي، مريح وأنيق.', 'أبيض'),
(12, NULL, 'Escarpins Talons 8cm', 'Escarpins en cuir verni avec talon aiguille 8cm. Élégance et sophistication.', 79.99, 25, 'Noir', 1, '2026-06-01 11:44:59', 'حذاء كعب 8 سم', 'حذاء كعب من جلد لامع بكعب رفيع 8 سم. أناقة ورقي.', 'أسود'),
(13, NULL, 'Baskets Cuir Blanc', 'Baskets en cuir véritable, semelle confort et design épuré. Polyvalentes et stylées.', 89.99, 35, 'Blanc', 1, '2026-06-01 11:44:59', 'حذاء رياضي جلد أبيض', 'حذاء رياضي من جلد حقيقي بنعل مريح وتصميم بسيط. عملي وأنيق.', 'أبيض'),
(14, NULL, 'Sac Main Chaîne Dorée', 'Sac à main avec chaîne dorée, compartiment principal et poche intérieure.', 65.99, 20, 'Camel', 1, '2026-06-01 11:44:59', 'حقيبة يد بسلسلة ذهبية', 'حقيبة يد بسلسلة ذهبية، قسم رئيسي وجيب داخلي.', 'جملي'),
(15, NULL, 'Collier Perles de Culture', 'Collier élégant avec perles de culture et fermoir en or. Intemporel et raffiné.', 45.99, 40, 'Blanc', 1, '2026-06-01 11:44:59', 'عقد لؤلؤ طبيعي', 'عقد أنيق بلؤلؤ طبيعي وقفل ذهبي. قطعة خالدة وراقية.', 'أبيض');

-- --------------------------------------------------------

--
-- Structure de la table `product_image`
--

DROP TABLE IF EXISTS `product_image`;
CREATE TABLE IF NOT EXISTS `product_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_64617F034584665A` (`product_id`),
  KEY `idx_product_image_product_position` (`product_id`,`position`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product_image`
--

INSERT INTO `product_image` (`id`, `product_id`, `filename`, `position`) VALUES
(11, 6, 'top-balloon-1.webp', 0),
(12, 6, 'top-balloon-2.webp', 1),
(13, 7, 'jean-slim-1.webp', 0),
(14, 7, 'jean-slim-2.webp', 1),
(15, 8, 'jupe-plissee-1.webp', 0),
(16, 8, 'jupe-plissee-2.webp', 1),
(17, 9, 'short-lin-1.webp', 0),
(18, 9, 'short-lin-2.webp', 1),
(19, 10, 'soutien-dentelle-1.webp', 0),
(20, 10, 'soutien-dentelle-2.webp', 1),
(21, 11, 'culotte-dentelle-1.webp', 0),
(22, 11, 'culotte-dentelle-2.webp', 1),
(23, 12, 'escarpins-1.webp', 0),
(24, 12, 'escarpins-2.webp', 1),
(25, 13, 'baskets-blanc-1.webp', 0),
(26, 13, 'baskets-blanc-2.webp', 1),
(27, 14, 'sac-chaine-1.webp', 0),
(28, 14, 'sac-chaine-2.webp', 1),
(29, 15, 'collier-perles-1.webp', 0),
(30, 15, 'collier-perles-2.webp', 1),
(31, 1, 'tenue-printemps-look-6a1d87328d00f.webp', 0),
(32, 2, 'TSR67EPZNNG5ZJ26JQKE4DVNBI-6a1dc2001972c.webp', 0),
(33, 3, 'Image71-6a1eb825f16df.webp', 0),
(34, 4, '3-6a1eb8b0673bf.webp', 0),
(35, 5, 't-shirt-basique-col-v-en-lin-melange-beige-cxg91-1-hd1-6a1eb8e79f8cd.webp', 0);

-- --------------------------------------------------------

--
-- Structure de la table `product_sizes`
--

DROP TABLE IF EXISTS `product_sizes`;
CREATE TABLE IF NOT EXISTS `product_sizes` (
  `product_id` int NOT NULL,
  `size_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`size_id`),
  KEY `IDX_17C2FC354584665A` (`product_id`),
  KEY `IDX_17C2FC35498DA827` (`size_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product_sizes`
--

INSERT INTO `product_sizes` (`product_id`, `size_id`) VALUES
(1, 2),
(1, 10),
(1, 15),
(2, 2),
(2, 10),
(2, 15),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(4, 3),
(4, 12),
(4, 16),
(4, 17),
(5, 10),
(5, 15);

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ar` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_C11D7DD14584665A` (`product_id`),
  KEY `idx_promotion_product_active_discount` (`product_id`,`is_active`,`discount`),
  KEY `idx_promotion_dates` (`start_date`,`end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `promotion`
--

INSERT INTO `promotion` (`id`, `product_id`, `title`, `description`, `discount`, `is_active`, `created_at`, `updated_at`, `start_date`, `end_date`, `title_ar`, `description_ar`) VALUES
(1, 1, 'aaaaaaa', 'aaaaaaaaaaaa', 12.00, 1, '2026-06-01 13:30:27', NULL, NULL, NULL, 'عرض تجريبي', 'وصف العرض التجريبي'),
(2, 3, 'qqqqqqqqqq', 'qqqqqqqqqqqq', 20.00, 1, '2026-06-04 15:17:46', NULL, NULL, NULL, 'عرض خاص', 'تفاصيل العرض الخاص');

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipping_fee` decimal(10,2) NOT NULL,
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` longtext COLLATE utf8mb4_unicode_ci,
  `seo_keywords` longtext COLLATE utf8mb4_unicode_ci,
  `seo_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_indexing_enabled` tinyint(1) NOT NULL,
  `seo_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ar` longtext COLLATE utf8mb4_unicode_ci,
  `seo_keywords_ar` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `settings`
--

INSERT INTO `settings` (`id`, `shipping_fee`, `seo_title`, `seo_description`, `seo_keywords`, `seo_image`, `seo_author`, `seo_indexing_enabled`, `seo_title_ar`, `seo_description_ar`, `seo_keywords_ar`) VALUES
(1, 3.99, 'Bella Couture - Mode feminine elegante', 'Boutique de mode feminine a Sousse: vetements elegants, collections tendance, tailles et couleurs au choix avec livraison en Tunisie.', 'mode feminine, boutique femme, vetements femme, Bella Couture, Sousse, Tunisie', 'logo/logo.webp', 'Bella Couture', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `size`
--

DROP TABLE IF EXISTS `size`;
CREATE TABLE IF NOT EXISTS `size` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name_ar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_size_active_position` (`is_active`,`position`),
  KEY `idx_size_code_active` (`code`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `size`
--

INSERT INTO `size` (`id`, `name`, `code`, `type`, `position`, `is_active`, `created_at`, `updated_at`, `name_ar`) VALUES
(1, '32', '32', 'clothing', 0, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '32'),
(2, '34', '34', 'clothing', 1, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '34'),
(3, '36', '36', 'clothing', 2, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '36'),
(4, '38', '38', 'clothing', 3, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '38'),
(5, '40', '40', 'clothing', 4, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '40'),
(6, '42', '42', 'clothing', 5, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '42'),
(7, '44', '44', 'clothing', 6, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '44'),
(8, '46', '46', 'clothing', 7, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '46'),
(9, '48', '48', 'clothing', 8, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '48'),
(10, 'Extra Small', 'XS', 'lingerie', 0, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'صغير جدا'),
(11, 'Small', 'S', 'lingerie', 1, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'صغير'),
(12, 'Medium', 'M', 'lingerie', 2, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'متوسط'),
(13, 'Large', 'L', 'lingerie', 3, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'كبير'),
(14, 'Extra Large', 'XL', 'lingerie', 4, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', 'كبير جدا'),
(15, '35', '35', 'shoes', 0, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '35'),
(16, '36', '36', 'shoes', 1, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '36'),
(17, '37', '37', 'shoes', 2, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '37'),
(18, '38', '38', 'shoes', 3, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '38'),
(19, '39', '39', 'shoes', 4, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '39'),
(20, '40', '40', 'shoes', 5, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '40'),
(21, '41', '41', 'shoes', 6, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '41'),
(22, '42', '42', 'shoes', 7, 1, '2026-06-01 11:44:56', '2026-06-01 11:44:56', '42');

-- --------------------------------------------------------

--
-- Structure de la table `slider_image`
--

DROP TABLE IF EXISTS `slider_image`;
CREATE TABLE IF NOT EXISTS `slider_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ar` longtext COLLATE utf8mb4_unicode_ci,
  `button_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `slider_image`
--

INSERT INTO `slider_image` (`id`, `filename`, `title`, `description`, `button_text`, `button_url`, `position`, `is_active`, `created_at`, `title_ar`, `description_ar`, `button_text_ar`) VALUES
(1, 'tenue-printemps-look-6a1d8641cb85d.webp', 'Nouvelle Collection Printemps', 'Découvrez les nouvelles tendances mode femme de la saison', 'Découvrir', '/categories', 1, 1, '2026-06-01 11:44:59', 'تشكيلة الربيع الجديدة', 'اكتشفي أحدث صيحات الموضة النسائية لهذا الموسم', 'اكتشفي'),
(2, 'TSR67EPZNNG5ZJ26JQKE4DVNBI-6a1dc2172da9f.webp', 'Robes d\'Été', 'Des robes légères et élégantes pour briller cet été', 'Voir les robes', '/categorie/robes', 2, 1, '2026-06-01 11:44:59', 'فساتين الصيف', 'فساتين خفيفة وأنيقة للتألق هذا الصيف', 'عرض الفساتين'),
(3, 'soldes-6a1dc2719f7c6.webp', 'Soldes Exclusives', 'Jusqu\'à -50% sur une sélection d\'articles', 'Profiter des soldes', '/promotions', 3, 1, '2026-06-01 11:44:59', 'تخفيضات حصرية', 'حتى 50% على مجموعة مختارة من المنتجات', 'استفيدي من التخفيضات'),
(4, 'delivery-person-giving-cleaned-garments-600nw-2713940907-6a1dc2d5892bb.webp', 'Livraison Offerte', 'Livraison gratuite dès 60€ d\'achat', 'En savoir plus', '/livraison', 4, 1, '2026-06-01 11:44:59', 'توصيل مجاني', 'توصيل مجاني بداية من 60 د.ت شراء', 'معرفة المزيد');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `is_admin`, `created_at`) VALUES
(7, 'admin@admin.com', '[\"ROLE_ADMIN\"]', '$2y$13$atbSue3ULzE/4Sy3VYzQR.hB6yXv0ve.yZvNGL3dj957diSF8LeP6', 'Admin', 'Zina', 1, '2026-06-04 09:36:33');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F094584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `product_image`
--
ALTER TABLE `product_image`
  ADD CONSTRAINT `FK_64617F034584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `FK_17C2FC354584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_17C2FC35498DA827` FOREIGN KEY (`size_id`) REFERENCES `size` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `promotion`
--
ALTER TABLE `promotion`
  ADD CONSTRAINT `FK_C11D7DD14584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
