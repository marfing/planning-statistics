<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180611111839 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_A9001337ADF3F363 ON statistica_rete');
        $this->addSql('ALTER TABLE network_element DROP INDEX nome_2, ADD UNIQUE INDEX UNIQ_E5F3895354BD530C (nome)');
        $this->addSql('DROP INDEX nome ON network_element');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX nome ON network_element (nome)');
        $this->addSql('ALTER TABLE network_element RENAME INDEX uniq_e5f3895354bd530c TO nome_2');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A9001337ADF3F363 ON statistica_rete (data)');
    }
}
