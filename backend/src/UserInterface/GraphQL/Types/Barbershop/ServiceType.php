<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use App\Domain\Barbershop\Entity\Service;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class ServiceType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'BarbershopService',
        'description' => 'A barbershop service (e.g. haircut, beard trim)',
        'model'       => Service::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'Unique service identifier',
                'resolve'     => fn(Service $s): string => $s->getId()->toString(),
            ],
            'name' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Name of the service',
                'resolve'     => fn(Service $s): string => $s->getName(),
            ],
            'durationMinutes' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'Duration of the service in minutes',
                'resolve'     => fn(Service $s): int => $s->getDurationMinutes(),
            ],
            'price' => [
                'type'        => Type::nonNull(Type::float()),
                'description' => 'Price of the service',
                'resolve'     => fn(Service $s): float => $s->getPrice(),
            ],
            'currency' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Currency code (e.g. CZK, EUR)',
                'resolve'     => fn(Service $s): string => $s->getCurrency(),
            ],
            'stylists' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopStylistConnection')),
                'description' => 'Stylists available for this service',
                'args'        => [
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::string()],
                ],
                'resolve' => fn(Service $s): array => [
                    'edges' => $s->getBusiness()->getStylists()->toArray(),
                ],
            ],
        ];
    }
}
