<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318123908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP INDEX UNIQ_B6BD307FE92F8F78, ADD INDEX IDX_B6BD307FE92F8F78 (recipient_id)');
        $this->addSql('ALTER TABLE message DROP INDEX UNIQ_B6BD307FF624B39D, ADD INDEX IDX_B6BD307FF624B39D (sender_id)');
        $this->addSql('ALTER TABLE message ADD is_read TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP INDEX IDX_B6BD307FF624B39D, ADD UNIQUE INDEX UNIQ_B6BD307FF624B39D (sender_id)');
        $this->addSql('ALTER TABLE message DROP INDEX IDX_B6BD307FE92F8F78, ADD UNIQUE INDEX UNIQ_B6BD307FE92F8F78 (recipient_id)');
        $this->addSql('ALTER TABLE message DROP is_read');
    }
}
