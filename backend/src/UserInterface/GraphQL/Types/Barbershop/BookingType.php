<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Entity\Stylist;
use DateTimeInterface;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class BookingType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'BarbershopBooking',
        'description' => 'A barbershop booking/reservation',
        'model'       => Booking::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'Unique booking identifier',
                'resolve'     => fn(Booking $booking): string => $booking->getId()->toString(),
            ],
            'service' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopService')),
                'description' => 'The service booked',
                'resolve'     => fn(Booking $booking): Service => $booking->getService(),
            ],
            'stylist' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopStylist')),
                'description' => 'The stylist for this booking',
                'resolve'     => fn(Booking $booking): Stylist => $booking->getStylist(),
            ],
            'startTime' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Start time in ISO 8601 format',
                'resolve'     => fn(Booking $booking): string => $booking->getStartTime()->format(DateTimeInterface::ATOM),
            ],
            'endTime' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'End time in ISO 8601 format',
                'resolve'     => fn(Booking $booking): string => $booking->getEndTime()->format(DateTimeInterface::ATOM),
            ],
            'status' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopBookingStatus')),
                'description' => 'Current booking status',
                'resolve'     => fn(Booking $booking): string => strtoupper($booking->getStatus()->value),
            ],
            'customerName' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Name of the customer',
                'resolve'     => fn(Booking $booking): string => $booking->getCustomerName(),
            ],
            'customerContact' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Contact info (email or phone) of the customer',
                'resolve'     => fn(Booking $booking): string => $booking->getCustomerContact(),
            ],
        ];
    }
}
