<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MariaDB1060Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration
 */
final class Version20260503113051 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'DoctrineMigrations';
    }

    /**
     * {@inheritDoc}
     */
    public function isTransactional(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MariaDB1060Platform, 'Migration can only be executed safely on \'mariadb 10.6\' and higher.');

        $this->addSql('DROP INDEX UNIQ_52C540C8841CB121 ON notification_subscriptions');
        $this->addSql('ALTER TABLE notification_subscriptions CHANGE uri resource_scope VARCHAR(254) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52C540C82B142FE1 ON notification_subscriptions (resource_scope)');
    }

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MariaDB1060Platform, 'Migration can only be executed safely on \'mariadb 10.6\' and higher.');

        $this->addSql('DROP INDEX UNIQ_52C540C82B142FE1 ON notification_subscriptions');
        $this->addSql('ALTER TABLE notification_subscriptions CHANGE resource_scope uri VARCHAR(254) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52C540C8841CB121 ON notification_subscriptions (uri)');
    }
}
