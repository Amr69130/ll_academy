<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250818132038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration safe : colonnes adress déjà renommées';
    }

    public function up(Schema $schema): void
    {
        // plus aucune action à faire, les colonnes ont déjà été corrigées
    }

    public function down(Schema $schema): void
    {
        // rien à rollback, les colonnes originales n’existent plus
    }
}
