<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200414221050 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE batiment CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) DEFAULT NULL, CHANGE longueur longueur DOUBLE PRECISION DEFAULT NULL, CHANGE largeur largeur DOUBLE PRECISION DEFAULT NULL, CHANGE rayon rayon DOUBLE PRECISION DEFAULT NULL, CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bureau CHANGE entreprise_id entreprise_id INT DEFAULT NULL, CHANGE batiment_id batiment_id INT DEFAULT NULL, CHANGE numero numero INT DEFAULT NULL, CHANGE etage etage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD role VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE batiment CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE longueur longueur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE largeur largeur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE rayon rayon DOUBLE PRECISION DEFAULT \'NULL\', CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE bureau CHANGE entreprise_id entreprise_id INT DEFAULT NULL, CHANGE batiment_id batiment_id INT DEFAULT NULL, CHANGE numero numero INT DEFAULT NULL, CHANGE etage etage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP role');
    }
}
