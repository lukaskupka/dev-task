<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use App\Domain\Barbershop\Entity\Business;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class BusinessEdgeType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'BarbershopBusinessEdge',
        'description' => 'An edge in a BusinessConnection',
    ];

    public function fields(): array
    {
        return [
            'cursor' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'A cursor for pagination',
                'resolve'     => fn(Business $business): string => base64_encode($business->getId()->toString()),
            ],
            'node' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopBusiness')),
                'description' => 'The business node',
                'resolve'     => fn(Business $business): Business => $business,
            ],
        ];
    }
}
