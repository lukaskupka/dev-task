<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use App\Domain\Barbershop\Entity\Stylist;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class StylistEdgeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopStylistEdge',
        'description' => 'An edge in a StylistConnection',
    ];

    public function fields(): array
    {
        return [
            'cursor' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A cursor for pagination',
                'resolve' => fn(Stylist $stylist): string => base64_encode($stylist->getId()->toString()),
            ],
            'node' => [
                'type' => Type::nonNull(GraphQL::type('BarbershopStylist')),
                'description' => 'The stylist node',
                'resolve' => fn(Stylist $stylist): Stylist => $stylist,
            ],
        ];
    }
}
