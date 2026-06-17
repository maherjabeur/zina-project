<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602124500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'No-op: final schema is imported by the initial deployment migration.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
    }

    public function down(Schema $schema): void
    {
    }
}
