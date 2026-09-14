<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Command\RejectBooking;

final class RejectBookingCommand
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $stylistId,
        public readonly ?string $reason,
    ) {}
}
