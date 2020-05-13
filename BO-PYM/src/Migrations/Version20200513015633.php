<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200513015633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, $service_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_E00CEDDE53148C29 ($service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_categorie (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, img_url VARCHAR(255) DEFAULT NULL, actions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_278CF54FE7A1254A (contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, published DATETIME NOT NULL, updated DATETIME DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, $categorie_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, subtitle VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, img_url VARCHAR(255) DEFAULT NULL, actions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', telephone VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, INDEX IDX_E19D9AD2BB9346A3 ($categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, primary_color VARCHAR(255) DEFAULT NULL, img_url VARCHAR(255) DEFAULT NULL, action LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE53148C29 FOREIGN KEY ($service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE contact_categorie ADD CONSTRAINT FK_278CF54FE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2BB9346A3 FOREIGN KEY ($categorie_id) REFERENCES service_categorie (id)');
        $this->addSql('ALTER TABLE batiment CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) DEFAULT NULL, CHANGE longueur longueur DOUBLE PRECISION DEFAULT NULL, CHANGE largeur largeur DOUBLE PRECISION DEFAULT NULL, CHANGE rayon rayon DOUBLE PRECISION DEFAULT NULL, CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bureau CHANGE entreprise_id entreprise_id INT DEFAULT NULL, CHANGE batiment_id batiment_id INT DEFAULT NULL, CHANGE numero numero INT DEFAULT NULL, CHANGE etage etage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD token VARCHAR(255) DEFAULT NULL, ADD token_expires_at DATETIME DEFAULT NULL, ADD refresh_token VARCHAR(255) DEFAULT NULL, ADD refresh_token_expires_at DATETIME DEFAULT NULL, ADD is_email_verified TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE Role role VARCHAR(255) DEFAULT \'User\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B35F37A13B ON utilisateur (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3C74F2195 ON utilisateur (refresh_token)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE53148C29');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2BB9346A3');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE contact_categorie');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_categorie');
        $this->addSql('ALTER TABLE batiment CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE longueur longueur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE largeur largeur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE rayon rayon DOUBLE PRECISION DEFAULT \'NULL\', CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE bureau CHANGE entreprise_id entreprise_id INT DEFAULT NULL, CHANGE batiment_id batiment_id INT DEFAULT NULL, CHANGE numero numero INT DEFAULT NULL, CHANGE etage etage INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_1D1C63B35F37A13B ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3C74F2195 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP token, DROP token_expires_at, DROP refresh_token, DROP refresh_token_expires_at, DROP is_email_verified, CHANGE role Role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
