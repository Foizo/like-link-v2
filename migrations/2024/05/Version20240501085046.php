<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501085046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_urls DROP FOREIGN KEY FK_7E0593D69D1157C5');
        $this->addSql('ALTER TABLE shortcuts_urls DROP FOREIGN KEY FK_B533C2A6B93051D0');
        $this->addSql('DROP TABLE shortcuts_urls');
        $this->addSql('DROP INDEX UNIQ_7E0593D69D1157C5 ON customer_urls');
        $this->addSql('ALTER TABLE customer_urls DROP shortcut_url_id, CHANGE shortcuts_generated_shortcut shortcuts_generated_shortcut VARCHAR(13) DEFAULT NULL COLLATE `utf8_bin`, CHANGE shortcuts_customer_shortcut shortcuts_customer_shortcut VARCHAR(20) DEFAULT NULL COLLATE `utf8_bin`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shortcuts_urls (id INT AUTO_INCREMENT NOT NULL, app_domain_id INT NOT NULL, generated_shortcut VARCHAR(13) CHARACTER SET utf8mb3 DEFAULT \'not-generated\' NOT NULL COLLATE `utf8mb3_bin`, customer_shortcut VARCHAR(20) CHARACTER SET utf8mb3 DEFAULT \'not-specified\' NOT NULL COLLATE `utf8mb3_bin`, UNIQUE INDEX generated_customer_shortcut (generated_shortcut, customer_shortcut), INDEX IDX_B533C2A6B93051D0 (app_domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE shortcuts_urls ADD CONSTRAINT FK_B533C2A6B93051D0 FOREIGN KEY (app_domain_id) REFERENCES app_domains (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE customer_urls ADD shortcut_url_id INT DEFAULT NULL, CHANGE shortcuts_generated_shortcut shortcuts_generated_shortcut VARCHAR(13) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, CHANGE shortcuts_customer_shortcut shortcuts_customer_shortcut VARCHAR(20) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`');
        $this->addSql('ALTER TABLE customer_urls ADD CONSTRAINT FK_7E0593D69D1157C5 FOREIGN KEY (shortcut_url_id) REFERENCES shortcuts_urls (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7E0593D69D1157C5 ON customer_urls (shortcut_url_id)');
    }
}
