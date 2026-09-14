<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBooking;

final class GetBookingQuery
{
    public function __construct(
        public readonly string $id,
    ) {}
}
