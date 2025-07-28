<?php

declare(strict_types=1);

namespace CommerceDoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250728063412 extends AbstractMigration
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
            $this->write("-------- MIGRATING #2 shop_system_commerce{$databaseSufix}");

            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`client` 
                CHANGE COLUMN `updatedAt` `updated_at` DATETIME NOT NULL, 
                CHANGE COLUMN `createdAt` `created_at` DATETIME NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`order` 
                CHANGE COLUMN `completedAt` `completed_at` DATETIME DEFAULT NULL, 
                CHANGE COLUMN `createdAt` `created_at` DATETIME NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`order_product` 
                CHANGE COLUMN `productQuantity` `product_quantity` INT NOT NULL, 
                CHANGE COLUMN `productPricePerPiece` `product_price_per_piece` DOUBLE PRECISION NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`order_status_update` 
                CHANGE COLUMN `createdAt` `created_at` DATETIME NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`product` 
                CHANGE COLUMN `updatedAt` `updated_at` DATETIME NOT NULL, 
                CHANGE COLUMN `createdAt` `created_at` DATETIME NOT NULL, 
                CHANGE COLUMN `deletedAt` `deleted_at` DATETIME DEFAULT NULL"
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::DATABASE_MAPPER as $env => $databaseSufix) {
            $this->write("-------- MIGRATING #2 shop_system_commerce{$databaseSufix}");

            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`client` 
                CHANGE COLUMN `updated_at` `updatedAt` DATETIME NOT NULL, 
                CHANGE COLUMN `created_at` `createdAt` DATETIME NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`product` 
                CHANGE COLUMN `updated_at` `updatedAt` DATETIME NOT NULL, 
                CHANGE COLUMN `created_at` `createdAt` DATETIME NOT NULL, 
                CHANGE COLUMN `deleted_at` `deletedAt` DATETIME DEFAULT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`order_status_update` 
                CHANGE COLUMN `created_at` `createdAt` DATETIME NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`order` 
                CHANGE COLUMN `completed_at` `completedAt` DATETIME DEFAULT NULL, 
                CHANGE COLUMN `created_at` `createdAt` DATETIME NOT NULL"
            );
            $this->addSql("ALTER TABLE `shop_system_commerce{$databaseSufix}`.`order_product` 
                CHANGE COLUMN `product_quantity` `productQuantity` INT NOT NULL, 
                CHANGE COLUMN `product_price_per_piece` `productPricePerPiece` DOUBLE PRECISION NOT NULL"
            );
        }
    }
}
