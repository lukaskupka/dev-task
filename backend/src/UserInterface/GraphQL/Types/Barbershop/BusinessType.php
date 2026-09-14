<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use App\Domain\Barbershop\Entity\Business;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class BusinessType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'BarbershopBusiness',
        'description' => 'A barbershop business',
        'model'       => Business::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'Unique business identifier',
                'resolve'     => fn(Business $business): string => $business->getId()->toString(),
            ],
            'name' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Name of the business',
                'resolve'     => fn(Business $business): string => $business->getName(),
            ],
            'slug' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'URL-friendly identifier of the business',
                'resolve'     => fn(Business $business): string => $business->getSlug(),
            ],
            'services' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopServiceConnection')),
                'description' => 'Services offered by this business',
                'args'        => [
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::string()],
                ],
                'resolve' => fn(Business $business): array => ['edges' => $business->getServices()->toArray()],
            ],
            'stylists' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopStylistConnection')),
                'description' => 'Stylists working at this business',
                'args'        => [
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::string()],
                ],
                'resolve' => fn(Business $business): array => ['edges' => $business->getStylists()->toArray()],
            ],
        ];
    }
}
