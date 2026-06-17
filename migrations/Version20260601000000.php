<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601000000 extends AbstractMigration
{
    private const DUMP_TABLES = [
        'category',
        'size',
        'product',
        'user',
        'order',
        'order_item',
        'product_image',
        'product_sizes',
        'promotion',
        'settings',
        'slider_image',
    ];

    private const BOOLEAN_COLUMNS = [
        'category' => ['is_active'],
        'contact' => ['is_read'],
        'order' => ['notified', 'is_new'],
        'product' => ['is_active'],
        'promotion' => ['is_active'],
        'settings' => ['seo_indexing_enabled'],
        'size' => ['is_active'],
        'slider_image' => ['is_active'],
        'user' => ['is_admin'],
    ];

    public function getDescription(): string
    {
        return 'Create the database schema and import application data from zina-project.sql.';
    }

    public function up(Schema $schema): void
    {
        if (!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->importMysqlDump();
            return;
        }

        $this->createSchema($schema);
        $this->importDumpData();
        $this->resetSequences();
    }

    public function down(Schema $schema): void
    {
        $isMysql = $this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform;
        foreach (['promotion', 'product_sizes', 'product_image', 'order_item', 'order', 'product', 'slider_image', 'settings', 'contact', 'category', 'size', 'user', 'messenger_messages'] as $table) {
            $this->addSql(sprintf(
                'DROP TABLE IF EXISTS %s%s',
                $isMysql ? '`' . $table . '`' : $this->quoteTable($table),
                $isMysql ? '' : ' CASCADE'
            ));
        }
    }

    private function importMysqlDump(): void
    {
        $dumpPath = dirname(__DIR__) . '/zina-project.sql';
        if (!is_file($dumpPath)) {
            throw new \RuntimeException('Missing zina-project.sql. It is required to seed the MySQL database.');
        }

        $dump = (string) file_get_contents($dumpPath);
        $dump = preg_replace('/\/\*![\s\S]*?\*\/;/m', '', $dump) ?? $dump;
        $dump = preg_replace('/--\s*Structure de la table `doctrine_migration_versions`[\s\S]*?-- --------------------------------------------------------/m', '-- --------------------------------------------------------', $dump) ?? $dump;
        $dump = $this->makeMysqlDumpCompatibleWithSmallIndexLimits($dump);

        foreach ($this->splitSqlStatements($dump) as $statement) {
            $statement = $this->stripSqlComments($statement);
            if ($statement === '') {
                continue;
            }

            if (!preg_match('/^(DROP TABLE|CREATE TABLE|INSERT INTO|ALTER TABLE)\b/i', $statement)) {
                continue;
            }

            $this->addSql($statement);
        }
    }

    private function makeMysqlDumpCompatibleWithSmallIndexLimits(string $dump): string
    {
        $replacements = [
            '`slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL' => '`slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL',
            '`roles` json NOT NULL' => '`roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL',
            '`roles` JSON NOT NULL' => '`roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL',
            'UNIQUE KEY `UNIQ_64C19C1989D9B62` (`slug`)' => 'UNIQUE KEY `UNIQ_64C19C1989D9B62` (`slug`(100))',
            'KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)' => 'KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`(50),`available_at`,`delivered_at`,`id`)',
            'KEY `idx_order_status_created` (`status`,`created_at`)' => 'KEY `idx_order_status_created` (`status`(30),`created_at`)',
            'KEY `idx_order_number` (`order_number`)' => 'KEY `idx_order_number` (`order_number`(30))',
            'UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)' => 'UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`(100))',
        ];

        return strtr($dump, $replacements);
    }

    /**
     * @return list<string>
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $statement = '';
        $inString = false;
        $escaping = false;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $statement .= $char;

                if ($escaping) {
                    $escaping = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaping = true;
                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'") {
                $inString = true;
                $statement .= $char;
                continue;
            }

            if ($char === ';') {
                $statements[] = $statement;
                $statement = '';
                continue;
            }

            $statement .= $char;
        }

        if (trim($statement) !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }

    private function stripSqlComments(string $statement): string
    {
        $lines = preg_split('/\R/', $statement) ?: [];
        $lines = array_filter($lines, static function (string $line): bool {
            return !str_starts_with(ltrim($line), '--');
        });

        return trim(implode("\n", $lines));
    }

    private function createSchema(Schema $schema): void
    {
        if (!$schema->hasTable('category')) {
            $this->addSql('CREATE TABLE category (
                id SERIAL NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT DEFAULT NULL,
                color VARCHAR(50) DEFAULT NULL,
                icon VARCHAR(255) DEFAULT NULL,
                position INT NOT NULL,
                is_active BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                name_ar VARCHAR(255) DEFAULT NULL,
                description_ar TEXT DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
            $this->addSql('CREATE INDEX idx_category_active_position ON category (is_active, position)');
        }

        if (!$schema->hasTable('contact')) {
            $this->addSql('CREATE TABLE contact (
                id SERIAL NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) DEFAULT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                is_read BOOLEAN NOT NULL,
                PRIMARY KEY(id)
            )');
        }

        if (!$schema->hasTable('messenger_messages')) {
            $this->addSql('CREATE TABLE messenger_messages (
                id BIGSERIAL NOT NULL,
                body TEXT NOT NULL,
                headers TEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        }

        if (!$schema->hasTable('size')) {
            $this->addSql('CREATE TABLE size (
                id SERIAL NOT NULL,
                name VARCHAR(50) NOT NULL,
                code VARCHAR(20) NOT NULL,
                type VARCHAR(50) NOT NULL,
                position INT NOT NULL,
                is_active BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                name_ar VARCHAR(50) DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX idx_size_active_position ON size (is_active, position)');
            $this->addSql('CREATE INDEX idx_size_code_active ON size (code, is_active)');
        }

        if (!$schema->hasTable('user')) {
            $this->addSql('CREATE TABLE "user" (
                id SERIAL NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles TEXT NOT NULL,
                password VARCHAR(255) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                is_admin BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        }

        if (!$schema->hasTable('product')) {
            $this->addSql('CREATE TABLE product (
                id SERIAL NOT NULL,
                category_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                price NUMERIC(10, 2) NOT NULL,
                quantity INT NOT NULL,
                color VARCHAR(255) NOT NULL,
                is_active BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                name_ar VARCHAR(255) DEFAULT NULL,
                description_ar TEXT DEFAULT NULL,
                color_ar VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
            $this->addSql('CREATE INDEX idx_product_active_created ON product (is_active, created_at)');
            $this->addSql('CREATE INDEX idx_product_category_active ON product (category_id, is_active)');
        }

        if (!$schema->hasTable('order')) {
            $this->addSql('CREATE TABLE "order" (
                id SERIAL NOT NULL,
                user_id INT DEFAULT NULL,
                order_number VARCHAR(50) NOT NULL,
                customer_email VARCHAR(255) DEFAULT NULL,
                customer_name VARCHAR(255) NOT NULL,
                customer_phone VARCHAR(20) NOT NULL,
                total NUMERIC(10, 2) NOT NULL,
                status VARCHAR(50) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                notified BOOLEAN NOT NULL,
                shipping_address TEXT DEFAULT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                discount NUMERIC(10, 2) DEFAULT NULL,
                original_total NUMERIC(10, 2) DEFAULT NULL,
                shipping_fee NUMERIC(10, 2) DEFAULT NULL,
                is_new BOOLEAN DEFAULT TRUE NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
            $this->addSql('CREATE INDEX idx_order_status_created ON "order" (status, created_at)');
            $this->addSql('CREATE INDEX idx_order_number ON "order" (order_number)');
            $this->addSql('CREATE INDEX idx_order_new_created ON "order" (is_new, created_at)');
        }

        if (!$schema->hasTable('order_item')) {
            $this->addSql('CREATE TABLE order_item (
                id SERIAL NOT NULL,
                order_id INT NOT NULL,
                product_id INT DEFAULT NULL,
                quantity INT NOT NULL,
                unit_price NUMERIC(10, 2) NOT NULL,
                total NUMERIC(10, 2) NOT NULL,
                size VARCHAR(50) DEFAULT NULL,
                color VARCHAR(50) DEFAULT NULL,
                original_price NUMERIC(10, 2) DEFAULT NULL,
                discount NUMERIC(5, 2) DEFAULT NULL,
                promotion_title VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
            $this->addSql('CREATE INDEX IDX_52EA1F094584665A ON order_item (product_id)');
        }

        if (!$schema->hasTable('product_image')) {
            $this->addSql('CREATE TABLE product_image (
                id SERIAL NOT NULL,
                product_id INT DEFAULT NULL,
                filename VARCHAR(255) NOT NULL,
                position INT NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IDX_64617F034584665A ON product_image (product_id)');
            $this->addSql('CREATE INDEX idx_product_image_product_position ON product_image (product_id, position)');
        }

        if (!$schema->hasTable('product_sizes')) {
            $this->addSql('CREATE TABLE product_sizes (
                product_id INT NOT NULL,
                size_id INT NOT NULL,
                PRIMARY KEY(product_id, size_id)
            )');
            $this->addSql('CREATE INDEX IDX_17C2FC354584665A ON product_sizes (product_id)');
            $this->addSql('CREATE INDEX IDX_17C2FC35498DA827 ON product_sizes (size_id)');
        }

        if (!$schema->hasTable('promotion')) {
            $this->addSql('CREATE TABLE promotion (
                id SERIAL NOT NULL,
                product_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                discount NUMERIC(5, 2) NOT NULL,
                is_active BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                title_ar VARCHAR(255) DEFAULT NULL,
                description_ar TEXT DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IDX_C11D7DD14584665A ON promotion (product_id)');
            $this->addSql('CREATE INDEX idx_promotion_product_active_discount ON promotion (product_id, is_active, discount)');
            $this->addSql('CREATE INDEX idx_promotion_dates ON promotion (start_date, end_date)');
        }

        if (!$schema->hasTable('settings')) {
            $this->addSql('CREATE TABLE settings (
                id SERIAL NOT NULL,
                shipping_fee NUMERIC(10, 2) NOT NULL,
                seo_title VARCHAR(255) DEFAULT NULL,
                seo_description TEXT DEFAULT NULL,
                seo_keywords TEXT DEFAULT NULL,
                seo_image VARCHAR(255) DEFAULT NULL,
                seo_author VARCHAR(255) DEFAULT NULL,
                seo_indexing_enabled BOOLEAN NOT NULL,
                seo_title_ar VARCHAR(255) DEFAULT NULL,
                seo_description_ar TEXT DEFAULT NULL,
                seo_keywords_ar TEXT DEFAULT NULL,
                PRIMARY KEY(id)
            )');
        }

        if (!$schema->hasTable('slider_image')) {
            $this->addSql('CREATE TABLE slider_image (
                id SERIAL NOT NULL,
                filename VARCHAR(255) NOT NULL,
                title VARCHAR(255) DEFAULT NULL,
                description TEXT DEFAULT NULL,
                button_text VARCHAR(255) DEFAULT NULL,
                button_url VARCHAR(255) DEFAULT NULL,
                position INT NOT NULL,
                is_active BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                title_ar VARCHAR(255) DEFAULT NULL,
                description_ar TEXT DEFAULT NULL,
                button_text_ar VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            )');
        }

        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_sizes ADD CONSTRAINT FK_17C2FC354584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_sizes ADD CONSTRAINT FK_17C2FC35498DA827 FOREIGN KEY (size_id) REFERENCES size (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD14584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    private function importDumpData(): void
    {
        $dumpPath = dirname(__DIR__) . '/zina-project.sql';
        if (!is_file($dumpPath)) {
            throw new \RuntimeException('Missing zina-project.sql. It is required to seed the PostgreSQL database.');
        }

        $dump = (string) file_get_contents($dumpPath);

        foreach (self::DUMP_TABLES as $table) {
            foreach ($this->extractInserts($dump, $table) as [$columns, $rows]) {
                $sql = sprintf(
                    'INSERT INTO %s (%s) VALUES (%s) ON CONFLICT DO NOTHING',
                    $this->quoteTable($table),
                    implode(', ', array_map(fn (string $column): string => $this->quoteIdentifier($column), $columns)),
                    implode(', ', array_fill(0, count($columns), '?'))
                );

                foreach ($rows as $row) {
                    $params = [];
                    foreach ($columns as $index => $column) {
                        $params[] = $this->normalizeValue($table, $column, $row[$index] ?? null);
                    }

                    $this->addSql($sql, $params);
                }
            }
        }
    }

    /**
     * @return list<array{list<string>, list<list<string|null>>}>
     */
    private function extractInserts(string $dump, string $table): array
    {
        $pattern = sprintf('/INSERT INTO `%s` \((.*?)\) VALUES\s*(.*?);/s', preg_quote($table, '/'));
        if (!preg_match_all($pattern, $dump, $matches, \PREG_SET_ORDER)) {
            return [];
        }

        $inserts = [];
        foreach ($matches as $match) {
            $columns = array_map(
                static fn (string $column): string => trim($column, " `\r\n\t"),
                explode(',', $match[1])
            );

            $inserts[] = [$columns, $this->parseRows($match[2])];
        }

        return $inserts;
    }

    /**
     * @return list<list<string|null>>
     */
    private function parseRows(string $values): array
    {
        $rows = [];
        $row = [];
        $token = '';
        $inString = false;
        $escaping = false;
        $insideRow = false;
        $length = strlen($values);

        for ($i = 0; $i < $length; $i++) {
            $char = $values[$i];

            if ($inString) {
                if ($escaping) {
                    $token .= $char;
                    $escaping = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaping = true;
                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                    continue;
                }

                $token .= $char;
                continue;
            }

            if ($char === "'") {
                $inString = true;
                continue;
            }

            if ($char === '(') {
                $insideRow = true;
                $row = [];
                $token = '';
                continue;
            }

            if (!$insideRow) {
                continue;
            }

            if ($char === ',') {
                $row[] = $this->parseToken($token);
                $token = '';
                continue;
            }

            if ($char === ')') {
                $row[] = $this->parseToken($token);
                $rows[] = $row;
                $insideRow = false;
                $token = '';
                continue;
            }

            $token .= $char;
        }

        return $rows;
    }

    private function parseToken(string $token): ?string
    {
        $token = trim($token);

        return strtoupper($token) === 'NULL' ? null : $token;
    }

    private function normalizeValue(string $table, string $column, ?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (in_array($column, self::BOOLEAN_COLUMNS[$table] ?? [], true)) {
            return $value === '1';
        }

        return $value;
    }

    private function resetSequences(): void
    {
        foreach (['category', 'contact', 'messenger_messages', 'order', 'order_item', 'product', 'product_image', 'promotion', 'settings', 'size', 'slider_image', 'user'] as $table) {
            $quotedTable = $this->quoteTable($table);
            $sequence = sprintf('%s_id_seq', $table);
            $this->addSql(sprintf(
                "SELECT setval('%s', COALESCE((SELECT MAX(id) FROM %s), 1), (SELECT COUNT(*) FROM %s) > 0)",
                str_replace("'", "''", $sequence),
                $quotedTable,
                $quotedTable
            ));
        }
    }

    private function quoteTable(string $table): string
    {
        return in_array($table, ['order', 'user'], true) ? '"' . $table . '"' : $table;
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}
