<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261021094121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bird (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE brown (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bryozoan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cnidarian (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE crustacean (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE echinoderm (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE fish (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE glossary (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, example VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE green (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mammal (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mollusc (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE polychaete (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE red (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reptile (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sea (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sponge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tunicate (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dci VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, url VARCHAR(550) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bird');
        $this->addSql('DROP TABLE brown');
        $this->addSql('DROP TABLE bryozoan');
        $this->addSql('DROP TABLE cnidarian');
        $this->addSql('DROP TABLE crustacean');
        $this->addSql('DROP TABLE echinoderm');
        $this->addSql('DROP TABLE fish');
        $this->addSql('DROP TABLE glossary');
        $this->addSql('DROP TABLE green');
        $this->addSql('DROP TABLE mammal');
        $this->addSql('DROP TABLE mollusc');
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE polychaete');
        $this->addSql('DROP TABLE red');
        $this->addSql('DROP TABLE reptile');
        $this->addSql('DROP TABLE sea');
        $this->addSql('DROP TABLE sponge');
        $this->addSql('DROP TABLE tunicate');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
