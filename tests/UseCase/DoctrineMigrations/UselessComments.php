<?php

declare(strict_types=1);

namespace tests\UseCase\DoctrineMigrations;

use PedroTroller\CS\Fixer\DoctrineMigrationsFixer;
use tests\UseCase;

final class UselessComments implements UseCase
{
    public function getFixers(): iterable
    {
        yield new DoctrineMigrationsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            declare(strict_types=1);

            namespace Infrastructure\Doctrine\Migrations;

            use Doctrine\DBAL\Schema\Schema;
            use Doctrine\Migrations\AbstractMigration;

            /**
             * Auto-generated Migration: Please modify to your needs!
             */
            final class Version20190323095102 extends AbstractMigration
            {
                public function getDescription(): string
                {
                    return 'This is the description.';
                }

                public function up(Schema $schema): void
                {
                    // this up() migration is auto-generated, please modify it to your needs
                    $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

                    $this->addSql('CREATE TABLE admin (identifier CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
                }

                public function down(Schema $schema): void
                {
                    // this down() migration is auto-generated, please modify it to your needs
                    $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

                    $this->addSql('DROP TABLE admin');
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            declare(strict_types=1);

            namespace Infrastructure\Doctrine\Migrations;

            use Doctrine\DBAL\Schema\Schema;
            use Doctrine\Migrations\AbstractMigration;

            final class Version20190323095102 extends AbstractMigration
            {
                public function getDescription(): string
                {
                    return 'This is the description.';
                }

                public function up(Schema $schema): void
                {
                    $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

                    $this->addSql('CREATE TABLE admin (identifier CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
                }

                public function down(Schema $schema): void
                {
                    $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

                    $this->addSql('DROP TABLE admin');
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70100;
    }
}
