<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260617130500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ensure default administrator accounts exist after deployment.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('user')) {
            return;
        }

        $this->ensureAdmin(
            'admin@admin.com',
            '$2y$13$atbSue3ULzE/4Sy3VYzQR.hB6yXv0ve.yZvNGL3dj957diSF8LeP6',
            'Admin',
            'Zina'
        );

        $this->ensureAdmin(
            'admin@boutique-femme.com',
            '$2y$13$SToRtHSM16f6paqJH3LTaudZY6n1fXfuk.Nl6etPHRTDbPNeGouqS',
            'Admin',
            'Boutique'
        );
    }

    public function down(Schema $schema): void
    {
    }

    private function ensureAdmin(string $email, string $passwordHash, string $firstName, string $lastName): void
    {
        $table = $this->userTable();

        $this->addSql(
            sprintf(
                'INSERT INTO %s (email, roles, password, first_name, last_name, is_admin, created_at)
                 SELECT ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP
                 WHERE NOT EXISTS (SELECT 1 FROM %s WHERE email = ?)',
                $table,
                $table
            ),
            [
                $email,
                '["ROLE_ADMIN"]',
                $passwordHash,
                $firstName,
                $lastName,
                true,
                $email,
            ]
        );
    }

    private function userTable(): string
    {
        return $this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform ? '`user`' : '"user"';
    }
}
