<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Command\ConfirmBooking;

final class ConfirmBookingCommand
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $stylistId,
    ) {}
}
