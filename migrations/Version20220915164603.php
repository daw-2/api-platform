<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220915164603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('DROP INDEX IDX_232B318C12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, category_id, title, slug, content, image, created_at, updated_at, is_enabled FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_enabled BOOLEAN NOT NULL, CONSTRAINT FK_232B318C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, category_id, title, slug, content, image, created_at, updated_at, is_enabled) SELECT id, category_id, title, slug, content, image, created_at, updated_at, is_enabled FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C12469DE2 ON game (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_232B318C12469DE2');
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
