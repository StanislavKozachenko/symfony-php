<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505092237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, name VARCHAR(255) NOT NULL, release_date DATE NOT NULL, description VARCHAR(255) NOT NULL, cost NUMERIC(10, 0) NOT NULL, INDEX IDX_D34A04AD9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_service (product_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_304481624584665A (product_id), INDEX IDX_30448162ED5CA9E6 (service_id), PRIMARY KEY(product_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE product_service ADD CONSTRAINT FK_304481624584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_service ADD CONSTRAINT FK_30448162ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD9395C3F3');
        $this->addSql('ALTER TABLE product_service DROP FOREIGN KEY FK_304481624584665A');
        $this->addSql('ALTER TABLE product_service DROP FOREIGN KEY FK_30448162ED5CA9E6');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_service');
    }
}
