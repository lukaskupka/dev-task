<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use App\Domain\Barbershop\Entity\Service;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class ServiceEdgeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopServiceEdge',
        'description' => 'An edge in a ServiceConnection',
    ];

    public function fields(): array
    {
        return [
            'cursor' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A cursor for pagination',
                'resolve' => fn(Service $service): string => base64_encode($service->getId()->toString()),
            ],
            'node' => [
                'type' => Type::nonNull(GraphQL::type('BarbershopService')),
                'description' => 'The service node',
                'resolve' => fn(Service $service): Service => $service,
            ],
        ];
    }
}
