<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180717120227 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feasibility_b2b ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE feasibility_b2b ADD CONSTRAINT FK_5A794D76A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('CREATE INDEX IDX_5A794D76A76ED395 ON feasibility_b2b (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feasibility_b2b DROP FOREIGN KEY FK_5A794D76A76ED395');
        $this->addSql('DROP INDEX IDX_5A794D76A76ED395 ON feasibility_b2b');
        $this->addSql('ALTER TABLE feasibility_b2b DROP user_id');
    }
}
