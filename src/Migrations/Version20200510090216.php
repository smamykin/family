<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200510090216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, video_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A29C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A29C1004E FOREIGN KEY (video_id) REFERENCES videos (id)');
        $this->addSql('ALTER TABLE categories CHANGE parent parent INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL, CHANGE vimeo_api_key vimeo_api_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comments');
        $this->addSql('ALTER TABLE categories CHANGE parent parent INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE vimeo_api_key vimeo_api_key VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL');
    }
}
