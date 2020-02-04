<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200204150450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('DROP INDEX IDX_3AF34668727ACA70 ON categories');
        $this->addSql('ALTER TABLE categories ADD parent INT DEFAULT NULL, DROP parent_id');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF346683D8E604F FOREIGN KEY (parent) REFERENCES categories (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3AF346683D8E604F ON categories (parent)');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF346683D8E604F');
        $this->addSql('DROP INDEX IDX_3AF346683D8E604F ON categories');
        $this->addSql('ALTER TABLE categories ADD parent_id INT DEFAULT NULL, DROP parent');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL');
    }
}
