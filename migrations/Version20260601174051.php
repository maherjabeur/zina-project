<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260601174051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow multiple product colors and store shipping fee on orders.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD shipping_fee NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE color color VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP shipping_fee');
        $this->addSql('ALTER TABLE product CHANGE color color VARCHAR(50) NOT NULL');
    }
}
