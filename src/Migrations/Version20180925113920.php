<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180925113920 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE traffic_report (id INT AUTO_INCREMENT NOT NULL, router_in_id INT NOT NULL, router_out_id INT NOT NULL, router_in_name VARCHAR(255) DEFAULT NULL, router_in_ip VARCHAR(255) NOT NULL, router_out_name VARCHAR(255) DEFAULT NULL, router_out_ip VARCHAR(255) NOT NULL, bandwidth INT DEFAULT NULL, last_timestamp DATETIME NOT NULL, duration INT DEFAULT NULL, samples INT DEFAULT NULL, INDEX IDX_216B5BA35583B2E (router_in_id), INDEX IDX_216B5BA3671C95E6 (router_out_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE router (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, file_system_root_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE traffic_report ADD CONSTRAINT FK_216B5BA35583B2E FOREIGN KEY (router_in_id) REFERENCES router (id)');
        $this->addSql('ALTER TABLE traffic_report ADD CONSTRAINT FK_216B5BA3671C95E6 FOREIGN KEY (router_out_id) REFERENCES router (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE traffic_report DROP FOREIGN KEY FK_216B5BA35583B2E');
        $this->addSql('ALTER TABLE traffic_report DROP FOREIGN KEY FK_216B5BA3671C95E6');
        $this->addSql('DROP TABLE traffic_report');
        $this->addSql('DROP TABLE router');
    }
}
