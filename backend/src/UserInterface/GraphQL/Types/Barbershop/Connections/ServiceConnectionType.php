<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class ServiceConnectionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopServiceConnection',
        'description' => 'A connection to a list of services',
    ];

    public function fields(): array
    {
        return [
            'edges' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('BarbershopServiceEdge')))),
                'description' => 'A list of service edges',
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
