<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class UserErrorType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopUserError',
        'description' => 'A user-facing error from a barbershop mutation',
    ];

    public function fields(): array
    {
        return [
            'field' => [
                'type' => Type::string(),
                'description' => 'The field that caused the error, or null for general errors',
            ],
            'message' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A human-readable error message',
            ],
        ];
    }
}
