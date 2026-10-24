<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MariaDB1060Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration.
 */
final class Version20261024120326 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Add jazz-the-kiesch event category';
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

        $this->addSql('ALTER TABLE events CHANGE category category ENUM(\'none\', \'session\', \'jazztage\', \'jazz-and-literature\', \'jazz-the-kiesch\') DEFAULT \'none\' NOT NULL');
    }

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MariaDB1060Platform, 'Migration can only be executed safely on \'mariadb 10.6\' and higher.');

        $this->abortIf(
            false !== $this->connection->fetchOne("SELECT id FROM events WHERE category = 'jazz-the-kiesch' LIMIT 1"),
            'Cannot remove the jazz-the-kiesch category while events use it.',
        );

        $this->addSql('ALTER TABLE events CHANGE category category ENUM(\'none\', \'session\', \'jazztage\', \'jazz-and-literature\') DEFAULT \'none\' NOT NULL');
    }
}
