<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260616194000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a dedicated new-order flag for admin highlighting.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('order') && !$schema->getTable('order')->hasColumn('is_new')) {
            $this->addSql(sprintf('ALTER TABLE %s ADD is_new BOOLEAN DEFAULT TRUE NOT NULL', $this->orderTable()));
            $this->addSql(sprintf("UPDATE %s SET is_new = CASE WHEN status = 'pending' THEN TRUE ELSE FALSE END", $this->orderTable()));
        }

        if ($schema->hasTable('order') && !$schema->getTable('order')->hasIndex('idx_order_new_created')) {
            $this->addSql(sprintf('CREATE INDEX idx_order_new_created ON %s (is_new, created_at)', $this->orderTable()));
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('order') && $schema->getTable('order')->hasIndex('idx_order_new_created')) {
            if ($this->connection->getDatabasePlatform()->getName() === 'mysql') {
                $this->addSql(sprintf('DROP INDEX idx_order_new_created ON %s', $this->orderTable()));
            } else {
                $this->addSql('DROP INDEX idx_order_new_created');
            }
        }

        if ($schema->hasTable('order') && $schema->getTable('order')->hasColumn('is_new')) {
            $this->addSql(sprintf('ALTER TABLE %s DROP is_new', $this->orderTable()));
        }
    }

    private function orderTable(): string
    {
        return $this->connection->getDatabasePlatform()->getName() === 'mysql' ? '`order`' : '"order"';
    }
}
