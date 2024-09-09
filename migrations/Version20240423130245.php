<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423130245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament ADD winner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D95DFCD4B8 FOREIGN KEY (winner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D95DFCD4B8 ON tournament (winner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D95DFCD4B8');
        $this->addSql('DROP INDEX IDX_BD5FB8D95DFCD4B8 ON tournament');
        $this->addSql('ALTER TABLE tournament DROP winner_id');
    }
}
