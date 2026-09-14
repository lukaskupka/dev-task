<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use App\Domain\Barbershop\ValueObject\Slot;
use DateTimeInterface;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class SlotType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BarbershopSlot',
        'description' => 'An available time slot',
        'model' => Slot::class,
    ];

    public function fields(): array
    {
        return [
            'startTime' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Start time in ISO 8601 format',
                'resolve' => fn(Slot $slot): string => $slot->startTime->format(DateTimeInterface::ATOM),
            ],
            'endTime' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'End time in ISO 8601 format',
                'resolve' => fn(Slot $slot): string => $slot->endTime->format(DateTimeInterface::ATOM),
            ],
        ];
    }
}
