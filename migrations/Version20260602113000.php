<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update messenger queue indexes for current Symfony Messenger schema.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('messenger_messages')) {
            return;
        }

        $table = $schema->getTable('messenger_messages');
        foreach (['IDX_75EA56E0FB7336F0', 'IDX_75EA56E0E3BD61CE', 'IDX_75EA56E016BA31DB'] as $index) {
            if ($table->hasIndex($index)) {
                $this->addSql(sprintf('DROP INDEX %s', $index));
            }
        }

        if (!$table->hasIndex('IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750')) {
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }
}
