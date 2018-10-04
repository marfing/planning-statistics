<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181003151643 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comune ADD codice_citta_metropolitana VARCHAR(255) DEFAULT NULL, ADD denominazione_citta_metropolitana VARCHAR(255) DEFAULT NULL, DROP codice_cittàmetropolitana, DROP denominazione_cittàmetropolitana');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comune ADD codice_cittàmetropolitana VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD denominazione_cittàmetropolitana VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP codice_citta_metropolitana, DROP denominazione_citta_metropolitana');
    }
}
