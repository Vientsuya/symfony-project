<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240821111341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE thumbnails (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, file_name VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_52A4DF608DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE thumbnails ADD CONSTRAINT FK_52A4DF608DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thumbnails DROP FOREIGN KEY FK_52A4DF608DB60186');
        $this->addSql('DROP TABLE thumbnails');
    }
}
