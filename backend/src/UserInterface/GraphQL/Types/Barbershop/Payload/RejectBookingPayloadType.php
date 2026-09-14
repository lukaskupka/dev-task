<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Payload;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class RejectBookingPayloadType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'RejectBookingPayload',
        'description' => 'Payload returned after rejecting a booking',
    ];

    public function fields(): array
    {
        return [
            'booking' => [
                'type'        => GraphQL::type('BarbershopBooking'),
                'description' => 'The rejected booking, or null if an error occurred',
            ],
            'errors' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('BarbershopUserError')))),
                'description' => 'Validation or domain errors',
            ],
        ];
    }
}
