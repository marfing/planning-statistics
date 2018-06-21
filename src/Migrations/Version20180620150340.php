<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620150340 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE network_element (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(190) NOT NULL, capacity INT NOT NULL, capacity_type VARCHAR(255) NOT NULL, directory_statistiche VARCHAR(255) DEFAULT NULL, csv_capacity_type_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E5F3895354BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statistica_rete (id INT AUTO_INCREMENT NOT NULL, network_element_id INT NOT NULL, valore INT NOT NULL, data DATE NOT NULL, INDEX IDX_A9001337C64ED598 (network_element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statistica_rete ADD CONSTRAINT FK_A9001337C64ED598 FOREIGN KEY (network_element_id) REFERENCES network_element (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE statistica_rete DROP FOREIGN KEY FK_A9001337C64ED598');
        $this->addSql('DROP TABLE network_element');
        $this->addSql('DROP TABLE statistica_rete');
    }
}
