<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Payload;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class CreateBookingPayloadType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'CreateBookingPayload',
        'description' => 'Payload returned after creating a booking',
    ];

    public function fields(): array
    {
        return [
            'stylist' => [
                'type'        => GraphQL::type('BarbershopStylist'),
                'description' => 'The stylist for whom the booking was created, or null if an error occurred',
            ],
            'errors' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('BarbershopUserError')))),
                'description' => 'Validation or domain errors',
            ],
        ];
    }
}
