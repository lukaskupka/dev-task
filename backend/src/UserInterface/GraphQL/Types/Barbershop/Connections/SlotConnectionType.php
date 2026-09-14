<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class SlotConnectionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopSlotConnection',
        'description' => 'A connection to a list of slots',
    ];

    public function fields(): array
    {
        return [
            'edges' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('BarbershopSlotEdge')))),
                'description' => 'A list of slot edges',
                'resolve' => fn(array $connection): array => $connection['edges'],
            ],
            'pageInfo' => [
                'type' => Type::nonNull(GraphQL::type('BarbershopPageInfo')),
                'description' => 'Pagination information',
                'resolve' => fn(array $connection): array => [
                    'hasNextPage' => false,
                    'hasPreviousPage' => false,
                    'startCursor' => null,
                    'endCursor' => null,
                ],
            ],
        ];
    }
}
