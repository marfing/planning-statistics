<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180607132853 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE statistica_rete ADD network_element_id INT NOT NULL, DROP elemento_rete');
        $this->addSql('ALTER TABLE statistica_rete ADD CONSTRAINT FK_A9001337C64ED598 FOREIGN KEY (network_element_id) REFERENCES network_element (id)');
        $this->addSql('CREATE INDEX IDX_A9001337C64ED598 ON statistica_rete (network_element_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE statistica_rete DROP FOREIGN KEY FK_A9001337C64ED598');
        $this->addSql('DROP INDEX IDX_A9001337C64ED598 ON statistica_rete');
        $this->addSql('ALTER TABLE statistica_rete ADD elemento_rete VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP network_element_id');
    }
}
