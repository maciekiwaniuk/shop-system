<?php

declare(strict_types=1);

namespace AuthDoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250728064118 extends AbstractMigration
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
            $this->write("-------- MIGRATING #2 shop_system_auth{$databaseSufix}");

            $this->addSql("ALTER TABLE `shop_system_auth{$databaseSufix}`.`user` 
                CHANGE COLUMN `updatedAt` `updated_at` DATETIME NOT NULL, 
                CHANGE COLUMN `createdAt` `created_at` DATETIME NOT NULL"
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::DATABASE_MAPPER as $env => $databaseSufix) {
            $this->write("-------- MIGRATING #2 shop_system_auth{$databaseSufix}");

            $this->addSql("ALTER TABLE `shop_system_auth{$databaseSufix}`.`user` 
                CHANGE COLUMN `updated_at` `updatedAt` DATETIME NOT NULL, 
                CHANGE COLUMN `created_at` `createdAt` DATETIME NOT NULL"
            );
        }
    }
}
