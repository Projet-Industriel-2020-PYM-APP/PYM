<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200512190455 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE contact_categorie (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, img_url VARCHAR(255) DEFAULT NULL, actions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_278CF54FE7A1254A (contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_categorie ADD CONSTRAINT FK_278CF54FE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE batiment CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) DEFAULT NULL, CHANGE longueur longueur DOUBLE PRECISION DEFAULT NULL, CHANGE largeur largeur DOUBLE PRECISION DEFAULT NULL, CHANGE rayon rayon DOUBLE PRECISION DEFAULT NULL, CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking CHANGE $service_id $service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bureau CHANGE entreprise_id entreprise_id INT DEFAULT NULL, CHANGE batiment_id batiment_id INT DEFAULT NULL, CHANGE numero numero INT DEFAULT NULL, CHANGE etage etage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post CHANGE updated updated DATETIME DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE service CHANGE $categorie_id $categorie_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE img_url img_url VARCHAR(255) DEFAULT NULL, CHANGE actions actions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE service_categorie CHANGE primary_color primary_color VARCHAR(255) DEFAULT NULL, CHANGE img_url img_url VARCHAR(255) DEFAULT NULL, CHANGE action action LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE utilisateur ADD token VARCHAR(255) DEFAULT NULL, ADD token_expires_at DATETIME DEFAULT NULL, ADD refresh_token VARCHAR(255) DEFAULT NULL, ADD refresh_token_expires_at DATETIME DEFAULT NULL, ADD is_email_verified TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE Role role VARCHAR(255) DEFAULT \'User\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B35F37A13B ON utilisateur (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3C74F2195 ON utilisateur (refresh_token)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE contact_categorie');
        $this->addSql('ALTER TABLE batiment CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE longueur longueur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE largeur largeur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE rayon rayon DOUBLE PRECISION DEFAULT \'NULL\', CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE booking CHANGE $service_id $service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bureau CHANGE entreprise_id entreprise_id INT DEFAULT NULL, CHANGE batiment_id batiment_id INT DEFAULT NULL, CHANGE numero numero INT DEFAULT NULL, CHANGE etage etage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post CHANGE updated updated DATETIME DEFAULT \'NULL\', CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE service CHANGE $categorie_id $categorie_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE subtitle subtitle VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE img_url img_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE actions actions LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE telephone telephone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE service_categorie CHANGE primary_color primary_color VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE img_url img_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE action action LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:object)\'');
        $this->addSql('DROP INDEX UNIQ_1D1C63B35F37A13B ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3C74F2195 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP token, DROP token_expires_at, DROP refresh_token, DROP refresh_token_expires_at, DROP is_email_verified, CHANGE role Role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
