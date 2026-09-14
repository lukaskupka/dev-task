<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use App\Domain\Barbershop\Enum\BookingStatus;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260914120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Prevent duplicate bookings for the same stylist and start time';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "CREATE UNIQUE INDEX UNIQ_BOOKINGS_STYLIST_START_TIME ON barbershop_bookings (stylist_id, start_time) WHERE status != '" . BookingStatus::Rejected->value . "'"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_BOOKINGS_STYLIST_START_TIME');
    }
}
