<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class BarbershopPageInfoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopPageInfo',
        'description' => 'Relay-spec PageInfo type for barbershop connections',
    ];

    public function fields(): array
    {
        return [
            'hasNextPage' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether there are more items after this page',
            ],
            'hasPreviousPage' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether there are more items before this page',
            ],
            'startCursor' => [
                'type' => Type::string(),
                'description' => 'Cursor of the first edge in this page',
            ],
            'endCursor' => [
                'type' => Type::string(),
                'description' => 'Cursor of the last edge in this page',
            ],
        ];
    }
}
