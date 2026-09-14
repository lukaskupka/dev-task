<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use App\Domain\Barbershop\Entity\Booking;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class BookingEdgeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopBookingEdge',
        'description' => 'An edge in a BookingConnection',
    ];

    public function fields(): array
    {
        return [
            'cursor' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A cursor for pagination',
                'resolve' => fn(Booking $booking): string => base64_encode($booking->getId()->toString()),
            ],
            'node' => [
                'type' => Type::nonNull(GraphQL::type('BarbershopBooking')),
                'description' => 'The booking node',
                'resolve' => fn(Booking $booking): Booking => $booking,
            ],
        ];
    }
}
