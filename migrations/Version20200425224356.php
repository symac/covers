<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200425224356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cover_isbn (id INT AUTO_INCREMENT NOT NULL, cover_id INT DEFAULT NULL, value10 VARCHAR(10) NOT NULL, value13 VARCHAR(13) NOT NULL, UNIQUE INDEX UNIQ_F8C0BD0F922726E9 (cover_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cover_cover (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, small TINYINT(1) DEFAULT \'0\' NOT NULL, medium TINYINT(1) DEFAULT \'0\' NOT NULL, large TINYINT(1) DEFAULT \'0\' NOT NULL, ark VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cover_isbn ADD CONSTRAINT FK_F8C0BD0F922726E9 FOREIGN KEY (cover_id) REFERENCES cover_cover (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cover_isbn DROP FOREIGN KEY FK_F8C0BD0F922726E9');
        $this->addSql('DROP TABLE cover_isbn');
        $this->addSql('DROP TABLE cover_cover');
    }
}
