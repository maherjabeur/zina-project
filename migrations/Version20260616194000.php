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
            $this->addSql('ALTER TABLE "order" ADD is_new BOOLEAN DEFAULT TRUE NOT NULL');
            $this->addSql("UPDATE \"order\" SET is_new = CASE WHEN status = 'pending' THEN TRUE ELSE FALSE END");
        }

        if ($schema->hasTable('order') && !$schema->getTable('order')->hasIndex('idx_order_new_created')) {
            $this->addSql('CREATE INDEX idx_order_new_created ON "order" (is_new, created_at)');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('order') && $schema->getTable('order')->hasIndex('idx_order_new_created')) {
            $this->addSql('DROP INDEX idx_order_new_created');
        }

        if ($schema->hasTable('order') && $schema->getTable('order')->hasColumn('is_new')) {
            $this->addSql('ALTER TABLE "order" DROP is_new');
        }
    }
}
