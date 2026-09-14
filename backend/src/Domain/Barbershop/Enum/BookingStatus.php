<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Enum;

enum BookingStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Rejected  = 'rejected';
}
