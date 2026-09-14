<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Command\CreateBooking;

final class CreateBookingCommand
{
    public function __construct(
        public readonly string $stylistId,
        public readonly string $serviceId,
        public readonly string $startTime,
        public readonly string $customerName,
        public readonly string $customerContact,
    ) {}
}
