<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602124500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add storefront performance indexes for catalog, promotions, cart images, and orders.';
    }

    public function up(Schema $schema): void
    {
        $this->addIndex($schema, 'product', 'idx_product_active_created', ['is_active', 'created_at']);
        $this->addIndex($schema, 'product', 'idx_product_category_active', ['category_id', 'is_active']);
        $this->addIndex($schema, 'promotion', 'idx_promotion_product_active_discount', ['product_id', 'is_active', 'discount']);
        $this->addIndex($schema, 'promotion', 'idx_promotion_dates', ['start_date', 'end_date']);
        $this->addIndex($schema, 'category', 'idx_category_active_position', ['is_active', 'position']);
        $this->addIndex($schema, 'size', 'idx_size_active_position', ['is_active', 'position']);
        $this->addIndex($schema, 'size', 'idx_size_code_active', ['code', 'is_active']);
        $this->addIndex($schema, 'product_image', 'idx_product_image_product_position', ['product_id', 'position']);
        $this->addIndex($schema, 'order', 'idx_order_status_created', ['status', 'created_at']);
        $this->addIndex($schema, 'order', 'idx_order_number', ['order_number']);
    }

    public function down(Schema $schema): void
    {
        $this->dropIndex($schema, 'product', 'idx_product_active_created');
        $this->dropIndex($schema, 'product', 'idx_product_category_active');
        $this->dropIndex($schema, 'promotion', 'idx_promotion_product_active_discount');
        $this->dropIndex($schema, 'promotion', 'idx_promotion_dates');
        $this->dropIndex($schema, 'category', 'idx_category_active_position');
        $this->dropIndex($schema, 'size', 'idx_size_active_position');
        $this->dropIndex($schema, 'size', 'idx_size_code_active');
        $this->dropIndex($schema, 'product_image', 'idx_product_image_product_position');
        $this->dropIndex($schema, 'order', 'idx_order_status_created');
        $this->dropIndex($schema, 'order', 'idx_order_number');
    }

    private function addIndex(Schema $schema, string $tableName, string $indexName, array $columns): void
    {
        if (!$schema->hasTable($tableName) || $schema->getTable($tableName)->hasIndex($indexName)) {
            return;
        }

        $this->addSql(sprintf(
            'CREATE INDEX %s ON %s (%s)',
            $indexName,
            $this->quoteTable($tableName),
            implode(', ', $columns)
        ));
    }

    private function dropIndex(Schema $schema, string $tableName, string $indexName): void
    {
        if (!$schema->hasTable($tableName) || !$schema->getTable($tableName)->hasIndex($indexName)) {
            return;
        }

        if ($this->connection->getDatabasePlatform()->getName() === 'mysql') {
            $this->addSql(sprintf('DROP INDEX %s ON %s', $indexName, $this->quoteTable($tableName)));
            return;
        }

        $this->addSql(sprintf('DROP INDEX %s', $indexName));
    }

    private function quoteTable(string $tableName): string
    {
        if ($tableName !== 'order') {
            return $tableName;
        }

        return $this->connection->getDatabasePlatform()->getName() === 'mysql' ? '`order`' : '"order"';
    }
}
