<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241208191627 extends AbstractMigration
{
    private const array DATABASE_MAPPER = [
        'prod' => '',
        'test' => '_test',
    ];

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        foreach (self::DATABASE_MAPPER as $env => $databaseSufix) {
            $this->write("-------- MIGRATING shop_system_auth{$databaseSufix}");

            $this->addSql(
                "CREATE TABLE `shop_system_auth{$databaseSufix}`.`user` (
              id VARCHAR(255) NOT NULL,
              email VARCHAR(200) NOT NULL,
              password VARCHAR(255) NOT NULL,
              name VARCHAR(100) NOT NULL,
              surname VARCHAR(100) NOT NULL,
              roles JSON NOT NULL,
              updatedAt DATETIME NOT NULL,
              createdAt DATETIME NOT NULL,
              UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
              INDEX user_search_idx (email),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER
            SET
              utf8mb4 COLLATE `utf8mb4_unicode_ci`");
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::DATABASE_MAPPER as $env => $databaseSufix) {
            $this->addSql("DROP TABLE `shop_system_auth{$databaseSufix}`.`user`");
        }
    }
}
