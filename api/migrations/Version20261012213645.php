<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MariaDB1060Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Consolidate subscription schema changes while preserving newsletter subscriptions.
 */
final class Version20261012213645 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Consolidate subscription and notification schema changes';
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

        $this->addSql('CREATE TABLE confirmable_entities (uuid BINARY(16) NOT NULL, email VARCHAR(254) NOT NULL, creation_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, confirmation_id BINARY(16) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1EB208CD6BACE54E (confirmation_id), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notification_subscriptions (resource_scope VARCHAR(254) NOT NULL, uuid BINARY(16) NOT NULL, INDEX IDX_52C540C82B142FE1 (resource_scope), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE confirmable_entities ADD CONSTRAINT FK_1EB208CD6BACE54E FOREIGN KEY (confirmation_id) REFERENCES confirmation_contracts (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_subscriptions ADD CONSTRAINT FK_52C540C8D17F50A6 FOREIGN KEY (uuid) REFERENCES confirmable_entities (uuid) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE newsletter_subscriptions ADD COLUMN new_uuid BINARY(16) NULL');
        $this->addSql('UPDATE newsletter_subscriptions SET new_uuid = UNHEX(REPLACE(UUID(), \'-\', \'\'))');
        $this->addSql('INSERT INTO confirmable_entities (uuid, email, creation_time, confirmation_id, discriminator) SELECT ns.new_uuid, ns.email, ns.creation_time, ns.confirmation_id, \'newsletter_subscription\' FROM newsletter_subscriptions ns');

        $this->addSql('ALTER TABLE known_mails CHANGE handle handle ENUM(\'no-reply\', \'jazzletter\', \'developers\') NOT NULL');
        $this->addSql('ALTER TABLE newsletter_subscriptions DROP FOREIGN KEY `FK_B3C13B0B6BACE54E`');
        $this->addSql('DROP INDEX UNIQ_B3C13B0B6BACE54E ON newsletter_subscriptions');
        $this->addSql('DROP INDEX UNIQ_B3C13B0BE7927C74 ON newsletter_subscriptions');
        $this->addSql('ALTER TABLE newsletter_subscriptions MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE newsletter_subscriptions DROP email, DROP creation_time, DROP id, DROP confirmation_id, CHANGE new_uuid uuid BINARY(16) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD CONSTRAINT FK_B3C13B0BD17F50A6 FOREIGN KEY (uuid) REFERENCES confirmable_entities (uuid) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MariaDB1060Platform, 'Migration can only be executed safely on \'mariadb 10.6\' and higher.');

        $this->addSql('ALTER TABLE known_mails CHANGE handle handle ENUM(\'no-reply\', \'jazzletter\') NOT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');

        $this->addSql('ALTER TABLE newsletter_subscriptions DROP FOREIGN KEY FK_B3C13B0BD17F50A6');
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD COLUMN email VARCHAR(254) NULL, ADD COLUMN creation_time DATETIME NULL, ADD COLUMN confirmation_id BINARY(16) NULL, ADD COLUMN id INT NULL');
        $this->addSql('UPDATE newsletter_subscriptions ns INNER JOIN confirmable_entities ce ON ce.uuid = ns.uuid SET ns.email = ce.email, ns.creation_time = ce.creation_time, ns.confirmation_id = ce.confirmation_id');
        $this->addSql('SET @row_number = 0');
        $this->addSql('UPDATE newsletter_subscriptions SET id = (@row_number := @row_number + 1)');
        $this->addSql('ALTER TABLE newsletter_subscriptions DROP PRIMARY KEY, DROP uuid, MODIFY email VARCHAR(254) NOT NULL, MODIFY creation_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, MODIFY confirmation_id BINARY(16) NOT NULL, MODIFY id INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

        $this->addSql('ALTER TABLE notification_subscriptions DROP FOREIGN KEY FK_52C540C8D17F50A6');
        $this->addSql('CREATE TEMPORARY TABLE notification_confirmation_contracts AS SELECT confirmation_id AS uuid FROM confirmable_entities WHERE discriminator = \'notification_subscription\'');
        $this->addSql('DELETE FROM confirmable_entities WHERE discriminator = \'notification_subscription\'');
        $this->addSql('DELETE FROM confirmation_contracts WHERE uuid IN (SELECT uuid FROM notification_confirmation_contracts)');
        $this->addSql('DROP TEMPORARY TABLE notification_confirmation_contracts');
        $this->addSql('DROP TABLE notification_subscriptions');

        $this->addSql('ALTER TABLE confirmable_entities DROP FOREIGN KEY FK_1EB208CD6BACE54E');
        $this->addSql('DROP TABLE confirmable_entities');
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD CONSTRAINT `FK_B3C13B0B6BACE54E` FOREIGN KEY (confirmation_id) REFERENCES confirmation_contracts (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3C13B0B6BACE54E ON newsletter_subscriptions (confirmation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3C13B0BE7927C74 ON newsletter_subscriptions (email)');
    }
}
