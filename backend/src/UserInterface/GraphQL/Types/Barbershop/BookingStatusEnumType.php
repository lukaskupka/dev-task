<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use App\Domain\Barbershop\Enum\BookingStatus;
use Rebing\GraphQL\Support\EnumType;

final class BookingStatusEnumType extends EnumType
{
    protected $attributes = [
        'name' => 'BarbershopBookingStatus',
        'description' => 'Status of a barbershop booking',
        'values' => [
            'PENDING' => ['value' => 'PENDING', 'description' => 'Booking is pending stylist confirmation'],
            'CONFIRMED' => ['value' => 'CONFIRMED', 'description' => 'Booking has been confirmed by the stylist'],
            'REJECTED' => ['value' => 'REJECTED', 'description' => 'Booking has been rejected by the stylist'],
        ],
    ];
}
