<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402174210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE building_organisation (building_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_7D180C3B4D2A7E12 (building_id), INDEX IDX_7D180C3B9E6B1585 (organisation_id), PRIMARY KEY(building_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, number_of_people INT NOT NULL, INDEX IDX_729F519B4D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE building_organisation ADD CONSTRAINT FK_7D180C3B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE building_organisation ADD CONSTRAINT FK_7D180C3B9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE building_organisations DROP FOREIGN KEY FK_AA783E237A3DA19F');
        $this->addSql('ALTER TABLE building_organisations DROP FOREIGN KEY FK_AA783E234D2A7E12');
        $this->addSql('DROP TABLE building_organisations');
        $this->addSql('DROP TABLE organisations');
        $this->addSql('DROP TABLE rooms');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE building_organisations (building_id INT NOT NULL, organisations_id INT NOT NULL, INDEX IDX_AA783E237A3DA19F (organisations_id), INDEX IDX_AA783E234D2A7E12 (building_id), PRIMARY KEY(building_id, organisations_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE organisations (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE rooms (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, number_of_people INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE building_organisations ADD CONSTRAINT FK_AA783E237A3DA19F FOREIGN KEY (organisations_id) REFERENCES organisations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE building_organisations ADD CONSTRAINT FK_AA783E234D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE building_organisation DROP FOREIGN KEY FK_7D180C3B4D2A7E12');
        $this->addSql('ALTER TABLE building_organisation DROP FOREIGN KEY FK_7D180C3B9E6B1585');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B4D2A7E12');
        $this->addSql('DROP TABLE building_organisation');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE room');
    }
}
