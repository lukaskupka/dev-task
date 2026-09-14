<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBusinessBookings;

final class GetBusinessBookingsQuery
{
    public function __construct(
        public readonly string $businessId,
        public readonly ?string $status = null,
    ) {}
}
