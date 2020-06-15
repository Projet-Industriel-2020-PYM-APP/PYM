<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615175531 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE batiment ADD longitude_gps DOUBLE PRECISION DEFAULT NULL, ADD latitude_gps DOUBLE PRECISION DEFAULT NULL, CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) DEFAULT NULL, CHANGE longueur longueur DOUBLE PRECISION DEFAULT NULL, CHANGE largeur largeur DOUBLE PRECISION DEFAULT NULL, CHANGE rayon rayon DOUBLE PRECISION DEFAULT NULL, CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE img_url img_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE batiment DROP longitude_gps, DROP latitude_gps, CHANGE type_batiment_id type_batiment_id INT DEFAULT NULL, CHANGE forme_parametrique_id forme_parametrique_id INT DEFAULT NULL, CHANGE representation3d representation3d VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE longueur longueur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE largeur largeur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE rayon rayon DOUBLE PRECISION DEFAULT \'NULL\', CHANGE hauteur hauteur DOUBLE PRECISION DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE img_url img_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
