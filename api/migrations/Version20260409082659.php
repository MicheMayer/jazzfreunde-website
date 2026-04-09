<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MariaDB1060Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration
 */
final class Version20260409082659 extends AbstractMigration
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

        $this->addSql('CREATE TABLE confirmable_entities (uuid BINARY(16) NOT NULL, email VARCHAR(254) NOT NULL, creation_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, confirmation_id BINARY(16) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1EB208CDE7927C74 (email), UNIQUE INDEX UNIQ_1EB208CD6BACE54E (confirmation_id), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notification_subscriptions (uuid BINARY(16) NOT NULL, PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE confirmable_entities ADD CONSTRAINT FK_1EB208CD6BACE54E FOREIGN KEY (confirmation_id) REFERENCES confirmation_contracts (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_subscriptions ADD CONSTRAINT FK_52C540C8D17F50A6 FOREIGN KEY (uuid) REFERENCES confirmable_entities (uuid) ON DELETE CASCADE');

        // Add a temporary column to hold the new UUID for each subscription
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD COLUMN new_uuid BINARY(16) NULL');

        // Generate a unique UUID per row using MariaDB\'s UUID() function
        $this->addSql('UPDATE newsletter_subscriptions SET new_uuid = UNHEX(REPLACE(UUID(), \'-\', \'\'))');

        // Carry over email, creation_time, confirmation_id and the discriminator into the new parent table
        $this->addSql('INSERT INTO confirmable_entities (uuid, email, creation_time, confirmation_id, discriminator) SELECT ns.new_uuid, ns.email, ns.creation_time, ns.confirmation_id, \'newsletter_subscription\' FROM newsletter_subscriptions ns');

        $this->addSql('ALTER TABLE newsletter_subscriptions DROP FOREIGN KEY `FK_B3C13B0B6BACE54E`');
        $this->addSql('DROP INDEX UNIQ_B3C13B0BE7927C74 ON newsletter_subscriptions');
        $this->addSql('DROP INDEX UNIQ_B3C13B0B6BACE54E ON newsletter_subscriptions');
        $this->addSql('ALTER TABLE newsletter_subscriptions MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE newsletter_subscriptions DROP id, DROP email, DROP creation_time, DROP confirmation_id, CHANGE new_uuid uuid BINARY(16) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD CONSTRAINT FK_B3C13B0BD17F50A6 FOREIGN KEY (uuid) REFERENCES confirmable_entities (uuid) ON DELETE CASCADE');
    }

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MariaDB1060Platform, 'Migration can only be executed safely on \'mariadb 10.6\' and higher.');

        // Drop the FK from newsletter_subscriptions to confirmable_entities first so we can read data before dropping the parent table
        $this->addSql('ALTER TABLE newsletter_subscriptions DROP FOREIGN KEY FK_B3C13B0BD17F50A6');

        // Re-add the original columns (nullable initially so we can populate them before enforcing NOT NULL)
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD COLUMN email VARCHAR(254) NULL, ADD COLUMN creation_time DATETIME NULL, ADD COLUMN confirmation_id BINARY(16) NULL');

        // Recover email, creation_time and confirmation_id from confirmable_entities
        $this->addSql('UPDATE newsletter_subscriptions ns INNER JOIN confirmable_entities ce ON ce.uuid = ns.uuid SET ns.email = ce.email, ns.creation_time = ce.creation_time, ns.confirmation_id = ce.confirmation_id');

        // Add the id column (nullable first, converted to AUTO_INCREMENT PK below)
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD COLUMN id INT NULL');

        // Assign sequential integer IDs
        $this->addSql('SET @row_number = 0');
        $this->addSql('UPDATE newsletter_subscriptions SET id = (@row_number := @row_number + 1)');

        // Restore original structure: drop uuid, make id the AUTO_INCREMENT PK, enforce NOT NULL on restored columns
        $this->addSql('ALTER TABLE newsletter_subscriptions DROP PRIMARY KEY, DROP uuid, MODIFY email VARCHAR(254) NOT NULL, MODIFY creation_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, MODIFY confirmation_id BINARY(16) NOT NULL, MODIFY id INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

        // Drop the new tables (FKs on those tables must be dropped first)
        $this->addSql('ALTER TABLE confirmable_entities DROP FOREIGN KEY FK_1EB208CD6BACE54E');
        $this->addSql('ALTER TABLE notification_subscriptions DROP FOREIGN KEY FK_52C540C8D17F50A6');
        $this->addSql('DROP TABLE notification_subscriptions');
        $this->addSql('DROP TABLE confirmable_entities');

        // Restore original FK and unique indexes on newsletter_subscriptions
        $this->addSql('ALTER TABLE newsletter_subscriptions ADD CONSTRAINT `FK_B3C13B0B6BACE54E` FOREIGN KEY (confirmation_id) REFERENCES confirmation_contracts (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3C13B0BE7927C74 ON newsletter_subscriptions (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3C13B0B6BACE54E ON newsletter_subscriptions (confirmation_id)');
    }
}
