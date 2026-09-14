<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug column to barbershop_businesses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE barbershop_businesses ADD COLUMN slug VARCHAR(255) NOT NULL DEFAULT ''");
        $this->addSql("UPDATE barbershop_businesses SET slug = 'gentlemens-cut' WHERE id = '11111111-1111-1111-1111-111111111111'");
        $this->addSql("UPDATE barbershop_businesses SET slug = 'luxury-barber-studio' WHERE id = '22222222-2222-2222-2222-222222222222'");
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BUSINESSES_SLUG ON barbershop_businesses (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_BUSINESSES_SLUG');
        $this->addSql('ALTER TABLE barbershop_businesses DROP COLUMN slug');
    }
}
