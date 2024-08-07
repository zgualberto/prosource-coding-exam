<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806155125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__customer AS SELECT id, full_name, email, username, password, gender, country, city, phone FROM customer');
        $this->addSql('DROP TABLE customer');
        $this->addSql('CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, gender VARCHAR(10) NOT NULL, country VARCHAR(50) NOT NULL, city VARCHAR(50) NOT NULL, phone VARCHAR(50) NOT NULL)');
        $this->addSql('INSERT INTO customer (id, full_name, email, username, password, gender, country, city, phone) SELECT id, full_name, email, username, password, gender, country, city, phone FROM __temp__customer');
        $this->addSql('DROP TABLE __temp__customer');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09E7927C74 ON customer (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__customer AS SELECT id, full_name, email, username, password, gender, country, city, phone FROM customer');
        $this->addSql('DROP TABLE customer');
        $this->addSql('CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, gender VARCHAR(10) NOT NULL, country VARCHAR(50) NOT NULL, city VARCHAR(50) NOT NULL, phone VARCHAR(50) NOT NULL)');
        $this->addSql('INSERT INTO customer (id, full_name, email, username, password, gender, country, city, phone) SELECT id, full_name, email, username, password, gender, country, city, phone FROM __temp__customer');
        $this->addSql('DROP TABLE __temp__customer');
    }
}
