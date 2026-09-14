<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetMyBookings;

final class GetMyBookingsQuery
{
    public function __construct(
        public readonly string $stylistId,
        public readonly ?string $status,
    ) {}
}
