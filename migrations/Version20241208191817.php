<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208191817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `shop_system_commerce`.`client` (
          id VARCHAR(255) NOT NULL,
          email VARCHAR(200) NOT NULL,
          name VARCHAR(100) NOT NULL,
          surname VARCHAR(100) NOT NULL,
          updatedAt DATETIME NOT NULL,
          createdAt DATETIME NOT NULL,
          UNIQUE INDEX UNIQ_C7440455E7927C74 (email),
          INDEX client_search_idx (email),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `shop_system_commerce`.`order` (
          id VARCHAR(255) NOT NULL,
          completedAt DATETIME DEFAULT NULL,
          createdAt DATETIME NOT NULL,
          client_id VARCHAR(255) NOT NULL,
          INDEX IDX_F529939819EB6921 (client_id),
          INDEX order_search_idx (id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `shop_system_commerce`.`order_product` (
          id INT AUTO_INCREMENT NOT NULL,
          productQuantity INT NOT NULL,
          productPricePerPiece DOUBLE PRECISION NOT NULL,
          order_id VARCHAR(255) NOT NULL,
          product_id INT NOT NULL,
          INDEX IDX_2530ADE68D9F6D38 (order_id),
          INDEX IDX_2530ADE64584665A (product_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `shop_system_commerce`.`order_status_update` (
          id VARCHAR(255) NOT NULL,
          status VARCHAR(200) NOT NULL,
          createdAt DATETIME NOT NULL,
          order_id VARCHAR(255) NOT NULL,
          INDEX IDX_880CD5E78D9F6D38 (order_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `shop_system_commerce`.`product` (
          id INT AUTO_INCREMENT NOT NULL,
          name VARCHAR(200) NOT NULL,
          slug VARCHAR(200) NOT NULL,
          price DOUBLE PRECISION NOT NULL,
          deletedAt DATETIME DEFAULT NULL,
          updatedAt DATETIME NOT NULL,
          createdAt DATETIME NOT NULL,
          INDEX product_search_idx (slug),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE
          `shop_system_commerce`.`order`
        ADD
          CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES `shop_system_commerce`.`client` (id)');
        $this->addSql('ALTER TABLE
          `shop_system_commerce`.`order_product`
        ADD
          CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES `shop_system_commerce`.`order` (id)');
        $this->addSql('ALTER TABLE
          `shop_system_commerce`.`order_product`
        ADD
          CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES `shop_system_commerce`.`product` (id)');
        $this->addSql('ALTER TABLE
          `shop_system_commerce`.`order_status_update`
        ADD
          CONSTRAINT FK_880CD5E78D9F6D38 FOREIGN KEY (order_id) REFERENCES `shop_system_commerce`.`order` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `shop_system_commerce`.`order` DROP FOREIGN KEY FK_F529939819EB6921');
        $this->addSql('ALTER TABLE `shop_system_commerce`.`order_product` DROP FOREIGN KEY FK_2530ADE68D9F6D38');
        $this->addSql('ALTER TABLE `shop_system_commerce`.`order_product` DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE `shop_system_commerce`.`order_status_update` DROP FOREIGN KEY FK_880CD5E78D9F6D38');
        $this->addSql('DROP TABLE `shop_system_commerce`.`client`');
        $this->addSql('DROP TABLE `shop_system_commerce`.`order`');
        $this->addSql('DROP TABLE `shop_system_commerce`.`order_product`');
        $this->addSql('DROP TABLE `shop_system_commerce`.`order_status_update`');
        $this->addSql('DROP TABLE `shop_system_commerce`.`product`');
    }
}
