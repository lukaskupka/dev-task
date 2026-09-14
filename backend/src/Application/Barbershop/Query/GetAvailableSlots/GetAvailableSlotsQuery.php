<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetAvailableSlots;

final class GetAvailableSlotsQuery
{
    public function __construct(
        public readonly string $stylistId,
        public readonly string $serviceId,
        public readonly string $date, // YYYY-MM-DD
    ) {}
}
