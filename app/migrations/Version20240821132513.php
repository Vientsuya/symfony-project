<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240821132513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uq_thumbnails_filename ON thumbnails');
        $this->addSql('ALTER TABLE thumbnails CHANGE file_name fileName VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uq_thumbnails_filename ON thumbnails (fileName)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uq_thumbnails_filename ON thumbnails');
        $this->addSql('ALTER TABLE thumbnails CHANGE fileName file_name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uq_thumbnails_filename ON thumbnails (file_name)');
    }
}
