<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181003150112 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comune (id INT AUTO_INCREMENT NOT NULL, denominazione VARCHAR(255) NOT NULL, codice_comune VARCHAR(255) NOT NULL, codice_cittàmetropolitana VARCHAR(255) DEFAULT NULL, denominazione_cittàmetropolitana VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE copertura (id INT AUTO_INCREMENT NOT NULL, comune_id INT NOT NULL, id_scala VARCHAR(255) NOT NULL, regione INT NOT NULL, provincia INT NOT NULL, codice_comune VARCHAR(255) NOT NULL, frazione VARCHAR(255) DEFAULT NULL, particella_top VARCHAR(255) DEFAULT NULL, indirizzo VARCHAR(255) DEFAULT NULL, civico VARCHAR(255) DEFAULT NULL, scala_palazzina VARCHAR(255) DEFAULT NULL, codice_via VARCHAR(255) NOT NULL, id_building VARCHAR(255) NOT NULL, coordinate_building_latitudine VARCHAR(255) DEFAULT NULL, coordinate_building_longitudine VARCHAR(255) DEFAULT NULL, pop VARCHAR(255) DEFAULT NULL, totale_ui INT DEFAULT NULL, stato_building VARCHAR(255) DEFAULT NULL, stato_scala_palazzina VARCHAR(255) DEFAULT NULL, datat_rfcindicativa DATETIME DEFAULT NULL, data_rfceffettiva DATETIME DEFAULT NULL, data_rfaindicativa DATETIME DEFAULT NULL, data_rfaeffettiva DATETIME DEFAULT NULL, data_ultima_modifica_record DATETIME DEFAULT NULL, data_ultima_modifica_stato_building DATETIME DEFAULT NULL, data_ultima_variazione_stato_scala_palazzina DATETIME DEFAULT NULL, INDEX IDX_2CB4317F885878B0 (comune_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE copertura ADD CONSTRAINT FK_2CB4317F885878B0 FOREIGN KEY (comune_id) REFERENCES comune (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE copertura DROP FOREIGN KEY FK_2CB4317F885878B0');
        $this->addSql('DROP TABLE comune');
        $this->addSql('DROP TABLE copertura');
    }
}
