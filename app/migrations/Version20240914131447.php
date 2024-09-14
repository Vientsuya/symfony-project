<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240914131447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks ADD thumbnail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597FDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES thumbnails (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50586597FDFF2E92 ON tasks (thumbnail_id)');
        $this->addSql('ALTER TABLE thumbnails DROP FOREIGN KEY FK_52A4DF608DB60186');
        $this->addSql('DROP INDEX UNIQ_52A4DF608DB60186 ON thumbnails');
        $this->addSql('ALTER TABLE thumbnails DROP task_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597FDFF2E92');
        $this->addSql('DROP INDEX UNIQ_50586597FDFF2E92 ON tasks');
        $this->addSql('ALTER TABLE tasks DROP thumbnail_id');
        $this->addSql('ALTER TABLE thumbnails ADD task_id INT NOT NULL');
        $this->addSql('ALTER TABLE thumbnails ADD CONSTRAINT FK_52A4DF608DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52A4DF608DB60186 ON thumbnails (task_id)');
    }
}
