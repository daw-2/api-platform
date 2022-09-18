<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918095147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_232B318C12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, category_id, title, slug, content, image, created_at, updated_at, is_enabled FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_enabled BOOLEAN NOT NULL, CONSTRAINT FK_232B318C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_232B318CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, category_id, title, slug, content, image, created_at, updated_at, is_enabled) SELECT id, category_id, title, slug, content, image, created_at, updated_at, is_enabled FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C12469DE2 ON game (category_id)');
        $this->addSql('CREATE INDEX IDX_232B318CA76ED395 ON game (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_232B318C12469DE2');
        $this->addSql('DROP INDEX IDX_232B318CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, category_id, title, slug, content, image, created_at, updated_at, is_enabled FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_enabled BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO game (id, category_id, title, slug, content, image, created_at, updated_at, is_enabled) SELECT id, category_id, title, slug, content, image, created_at, updated_at, is_enabled FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C12469DE2 ON game (category_id)');
    }
}
