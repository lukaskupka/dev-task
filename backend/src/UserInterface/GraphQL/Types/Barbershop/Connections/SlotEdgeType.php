<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Connections;

use App\Domain\Barbershop\ValueObject\Slot;
use DateTimeInterface;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class SlotEdgeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopSlotEdge',
        'description' => 'An edge in a SlotConnection',
    ];

    public function fields(): array
    {
        return [
            'cursor' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A cursor for pagination',
                'resolve' => fn(Slot $slot): string => base64_encode($slot->startTime->format(DateTimeInterface::ATOM)),
            ],
            'node' => [
                'type' => Type::nonNull(GraphQL::type('BarbershopSlot')),
                'description' => 'The slot node',
                'resolve' => fn(Slot $slot): Slot => $slot,
            ],
        ];
    }
}
