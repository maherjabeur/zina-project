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
        $this->addColumn($schema, 'seo_title', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'seo_description', 'TEXT DEFAULT NULL');
        $this->addColumn($schema, 'seo_keywords', 'TEXT DEFAULT NULL');
        $this->addColumn($schema, 'seo_image', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'seo_author', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'seo_indexing_enabled', 'BOOLEAN DEFAULT TRUE NOT NULL');
        $this->addSql("UPDATE settings SET seo_title = 'Bella Couture - Mode feminine elegante' WHERE seo_title IS NULL");
        $this->addSql("UPDATE settings SET seo_description = 'Boutique de mode feminine a Sousse: vetements elegants, collections tendance, tailles et couleurs au choix avec livraison en Tunisie.' WHERE seo_description IS NULL");
        $this->addSql("UPDATE settings SET seo_keywords = 'mode feminine, boutique femme, vetements femme, Bella Couture, Sousse, Tunisie' WHERE seo_keywords IS NULL");
        $this->addSql("UPDATE settings SET seo_image = 'logo/logo.webp' WHERE seo_image IS NULL");
        $this->addSql("UPDATE settings SET seo_author = 'Bella Couture' WHERE seo_author IS NULL");
    }

    public function down(Schema $schema): void
    {
        foreach (['seo_title', 'seo_description', 'seo_keywords', 'seo_image', 'seo_author', 'seo_indexing_enabled'] as $column) {
            if ($schema->hasTable('settings') && $schema->getTable('settings')->hasColumn($column)) {
                $this->addSql(sprintf('ALTER TABLE settings DROP %s', $column));
            }
        }
    }

    private function addColumn(Schema $schema, string $columnName, string $definition): void
    {
        if (!$schema->hasTable('settings') || $schema->getTable('settings')->hasColumn($columnName)) {
            return;
        }

        $this->addSql(sprintf('ALTER TABLE settings ADD %s %s', $columnName, $definition));
    }
}
