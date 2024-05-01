<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430084544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_urls ADD shortcuts_generated_shortcut VARCHAR(13) DEFAULT NULL COLLATE `utf8_bin`, ADD shortcuts_customer_shortcut VARCHAR(20) DEFAULT NULL COLLATE `utf8_bin`');
        $this->addSql('CREATE UNIQUE INDEX generated_shortcut ON customer_urls (shortcuts_generated_shortcut)');
        $this->addSql('CREATE UNIQUE INDEX customer_shortcut ON customer_urls (shortcuts_customer_shortcut)');
        $this->addSql('CREATE UNIQUE INDEX generated_vs_customer_shortcut ON customer_urls (shortcuts_generated_shortcut, shortcuts_customer_shortcut)');
        $this->addSql('ALTER TABLE customer_urls DROP FOREIGN KEY FK_7E0593D69D1157C5');
        $this->addSql('ALTER TABLE customer_urls ADD CONSTRAINT FK_7E0593D69D1157C5 FOREIGN KEY (shortcut_url_id) REFERENCES shortcuts_urls (id)');
    }

    public function down(Schema $schema): void
    {}
}
