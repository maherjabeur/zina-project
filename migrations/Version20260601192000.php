<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601192000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add SEO settings for site metadata and indexing.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings ADD seo_title VARCHAR(255) DEFAULT NULL, ADD seo_description LONGTEXT DEFAULT NULL, ADD seo_keywords LONGTEXT DEFAULT NULL, ADD seo_image VARCHAR(255) DEFAULT NULL, ADD seo_author VARCHAR(255) DEFAULT NULL, ADD seo_indexing_enabled TINYINT(1) NOT NULL');
        $this->addSql("UPDATE settings SET seo_title = 'Bella Couture - Mode feminine elegante' WHERE seo_title IS NULL");
        $this->addSql("UPDATE settings SET seo_description = 'Boutique de mode feminine a Sousse: vetements elegants, collections tendance, tailles et couleurs au choix avec livraison en Tunisie.' WHERE seo_description IS NULL");
        $this->addSql("UPDATE settings SET seo_keywords = 'mode feminine, boutique femme, vetements femme, Bella Couture, Sousse, Tunisie' WHERE seo_keywords IS NULL");
        $this->addSql("UPDATE settings SET seo_image = 'logo/logo.png' WHERE seo_image IS NULL");
        $this->addSql("UPDATE settings SET seo_author = 'Bella Couture' WHERE seo_author IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings DROP seo_title, DROP seo_description, DROP seo_keywords, DROP seo_image, DROP seo_author, DROP seo_indexing_enabled');
    }
}
