<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:ensure-admin', description: 'Ensure default administrator accounts exist.')]
class EnsureAdminCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
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

        $output->writeln('Default administrator accounts are available.');

        return Command::SUCCESS;
    }

    private function ensureAdmin(string $email, string $passwordHash, string $firstName, string $lastName): void
    {
        if ($this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            $this->connection->executeStatement(
                'INSERT IGNORE INTO `user` (email, roles, password, first_name, last_name, is_admin, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)',
                [$email, '["ROLE_ADMIN"]', $passwordHash, $firstName, $lastName, 1]
            );

            return;
        }

        $this->connection->executeStatement(
            'INSERT INTO "user" (email, roles, password, first_name, last_name, is_admin, created_at)
             SELECT ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP
             WHERE NOT EXISTS (SELECT 1 FROM "user" WHERE email = ?)',
            [$email, '["ROLE_ADMIN"]', $passwordHash, $firstName, $lastName, true, $email]
        );
    }
}
