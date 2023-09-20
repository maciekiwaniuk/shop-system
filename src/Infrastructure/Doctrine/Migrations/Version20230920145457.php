<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920145457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, completed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order_status_update` (id VARCHAR(255) NOT NULL, order_id VARCHAR(255) NOT NULL, status VARCHAR(200) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_880CD5E78D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `orders_products` (id INT AUTO_INCREMENT NOT NULL, order_id VARCHAR(255) NOT NULL, product_id VARCHAR(255) NOT NULL, product_quantity INT NOT NULL, product_price_per_piece DOUBLE PRECISION NOT NULL, INDEX IDX_749C879C8D9F6D38 (order_id), INDEX IDX_749C879C4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `product` (id VARCHAR(255) NOT NULL, name VARCHAR(200) NOT NULL, price DOUBLE PRECISION NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id VARCHAR(255) NOT NULL, email VARCHAR(200) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) NOT NULL, roles JSON NOT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, last_login_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_login_user_agent VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), UNIQUE INDEX UNIQ_8D93D649E7769B0F (surname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `order_status_update` ADD CONSTRAINT FK_880CD5E78D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `orders_products` ADD CONSTRAINT FK_749C879C8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `orders_products` ADD CONSTRAINT FK_749C879C4584665A FOREIGN KEY (product_id) REFERENCES `product` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order_status_update` DROP FOREIGN KEY FK_880CD5E78D9F6D38');
        $this->addSql('ALTER TABLE `orders_products` DROP FOREIGN KEY FK_749C879C8D9F6D38');
        $this->addSql('ALTER TABLE `orders_products` DROP FOREIGN KEY FK_749C879C4584665A');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE `order_status_update`');
        $this->addSql('DROP TABLE `orders_products`');
        $this->addSql('DROP TABLE `product`');
        $this->addSql('DROP TABLE `user`');
    }
}
