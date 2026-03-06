<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301135751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carte_bancaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, prenom VARCHAR(100) NOT NULL, nom VARCHAR(100) NOT NULL, type VARCHAR(100) NOT NULL, numero INTEGER NOT NULL, expiration DATE NOT NULL, code_securiter VARCHAR(3) NOT NULL, user_id INTEGER DEFAULT NULL, CONSTRAINT FK_59E3C22DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_59E3C22DA76ED395 ON carte_bancaire (user_id)');
        $this->addSql('CREATE TABLE consultation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, age INTEGER NOT NULL, description CLOB NOT NULL, date DATE NOT NULL, payer BOOLEAN DEFAULT NULL, prix INTEGER DEFAULT NULL, duree INTEGER DEFAULT NULL, patient_id INTEGER DEFAULT NULL, medecin_id INTEGER DEFAULT NULL, CONSTRAINT FK_964685A66B899279 FOREIGN KEY (patient_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_964685A64F31A84 FOREIGN KEY (medecin_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_964685A66B899279 ON consultation (patient_id)');
        $this->addSql('CREATE INDEX IDX_964685A64F31A84 ON consultation (medecin_id)');
        $this->addSql('CREATE TABLE traitement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, medicament VARCHAR(255) NOT NULL, quantite INTEGER NOT NULL, contenant VARCHAR(255) NOT NULL, duree INTEGER NOT NULL, posologie VARCHAR(255) NOT NULL, consultation_id INTEGER NOT NULL, CONSTRAINT FK_2A356D2762FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2A356D2762FF6CDF ON traitement (consultation_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, ssn VARCHAR(15) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, genre VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE carte_bancaire');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE traitement');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
