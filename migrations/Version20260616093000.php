<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260616093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Arabic content fields for bilingual storefront and admin content.';
    }

    public function up(Schema $schema): void
    {
        $this->addColumn($schema, 'product', 'name_ar', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'product', 'description_ar', 'TEXT DEFAULT NULL');
        $this->addColumn($schema, 'product', 'color_ar', 'VARCHAR(255) DEFAULT NULL');

        $this->addColumn($schema, 'category', 'name_ar', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'category', 'description_ar', 'TEXT DEFAULT NULL');

        $this->addColumn($schema, 'size', 'name_ar', 'VARCHAR(50) DEFAULT NULL');

        $this->addColumn($schema, 'slider_image', 'title_ar', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'slider_image', 'description_ar', 'TEXT DEFAULT NULL');
        $this->addColumn($schema, 'slider_image', 'button_text_ar', 'VARCHAR(255) DEFAULT NULL');

        $this->addColumn($schema, 'promotion', 'title_ar', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'promotion', 'description_ar', 'TEXT DEFAULT NULL');

        $this->addColumn($schema, 'settings', 'seo_title_ar', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn($schema, 'settings', 'seo_description_ar', 'TEXT DEFAULT NULL');
        $this->addColumn($schema, 'settings', 'seo_keywords_ar', 'TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->dropColumn($schema, 'settings', 'seo_keywords_ar');
        $this->dropColumn($schema, 'settings', 'seo_description_ar');
        $this->dropColumn($schema, 'settings', 'seo_title_ar');

        $this->dropColumn($schema, 'promotion', 'description_ar');
        $this->dropColumn($schema, 'promotion', 'title_ar');

        $this->dropColumn($schema, 'slider_image', 'button_text_ar');
        $this->dropColumn($schema, 'slider_image', 'description_ar');
        $this->dropColumn($schema, 'slider_image', 'title_ar');

        $this->dropColumn($schema, 'size', 'name_ar');

        $this->dropColumn($schema, 'category', 'description_ar');
        $this->dropColumn($schema, 'category', 'name_ar');

        $this->dropColumn($schema, 'product', 'color_ar');
        $this->dropColumn($schema, 'product', 'description_ar');
        $this->dropColumn($schema, 'product', 'name_ar');
    }

    private function addColumn(Schema $schema, string $tableName, string $columnName, string $definition): void
    {
        if (!$schema->hasTable($tableName) || $schema->getTable($tableName)->hasColumn($columnName)) {
            return;
        }

        $this->addSql(sprintf('ALTER TABLE %s ADD %s %s', $tableName, $columnName, $definition));
    }

    private function dropColumn(Schema $schema, string $tableName, string $columnName): void
    {
        if (!$schema->hasTable($tableName) || !$schema->getTable($tableName)->hasColumn($columnName)) {
            return;
        }

        $this->addSql(sprintf('ALTER TABLE %s DROP %s', $tableName, $columnName));
    }
}
