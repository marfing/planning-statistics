<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180627144141 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE network_elements_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE network_element ADD network_elements_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE network_element ADD CONSTRAINT FK_E5F38953EFB5100E FOREIGN KEY (network_elements_type_id) REFERENCES network_elements_type (id)');
        $this->addSql('CREATE INDEX IDX_E5F38953EFB5100E ON network_element (network_elements_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE network_element DROP FOREIGN KEY FK_E5F38953EFB5100E');
        $this->addSql('DROP TABLE network_elements_type');
        $this->addSql('DROP INDEX IDX_E5F38953EFB5100E ON network_element');
        $this->addSql('ALTER TABLE network_element DROP network_elements_type_id');
    }
}
