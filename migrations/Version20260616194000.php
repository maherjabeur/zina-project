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
        $this->addSql('ALTER TABLE `order` ADD is_new TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('UPDATE `order` SET is_new = CASE WHEN status = \'pending\' THEN 1 ELSE 0 END');
        $this->addSql('CREATE INDEX idx_order_new_created ON `order` (is_new, created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_order_new_created ON `order`');
        $this->addSql('ALTER TABLE `order` DROP is_new');
    }
}
