<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250628135342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, is_from_id INT NOT NULL, is_to_id INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_B6BD307F6EF25FC3 (is_from_id), INDEX IDX_B6BD307F7CC9A915 (is_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6EF25FC3 FOREIGN KEY (is_from_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7CC9A915 FOREIGN KEY (is_to_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F6EF25FC3');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F7CC9A915');
        $this->addSql('DROP TABLE message');
    }
}
