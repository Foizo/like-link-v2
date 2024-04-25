<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425073209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE shortcuts_urls DROP INDEX generated_customer_shortcut_url_idx, ADD UNIQUE INDEX generated_customer_shortcut (generated_shortcut, customer_shortcut);");
        $this->addSql("ALTER TABLE shortcuts_urls CHANGE generated_shortcut generated_shortcut VARCHAR(13) DEFAULT 'not-generated' NOT NULL COLLATE `utf8_bin`, CHANGE customer_shortcut customer_shortcut VARCHAR(20) DEFAULT 'not-specified' NOT NULL COLLATE `utf8_bin`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
