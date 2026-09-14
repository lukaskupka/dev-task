<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop;

use App\Application\Barbershop\Query\GetAvailableSlots\GetAvailableSlotsQuery;
use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\ValueObject\Slot;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class StylistType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'BarbershopStylist',
        'description' => 'A barbershop stylist',
        'model'       => Stylist::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'Unique stylist identifier',
                'resolve'     => fn(Stylist $stylist): string => $stylist->getId()->toString(),
            ],
            'name' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Name of the stylist',
                'resolve'     => fn(Stylist $stylist): string => $stylist->getName(),
            ],
            'photoUrl' => [
                'type'        => Type::string(),
                'description' => 'URL of the stylist photo',
                'resolve'     => fn(Stylist $stylist): ?string => $stylist->getPhotoUrl(),
            ],
            'availableSlots' => [
                'type'        => Type::nonNull(GraphQL::type('BarbershopSlotConnection')),
                'description' => 'Available time slots for this stylist',
                'args'        => [
                    'date'      => [
                        'type'        => Type::nonNull(Type::string()),
                        'description' => 'Date in YYYY-MM-DD format',
                    ],
                    'serviceId' => [
                        'type'        => Type::nonNull(Type::id()),
                        'description' => 'ID of the service to book',
                    ],
                    'first' => ['type' => Type::int()],
                    'after' => ['type' => Type::string()],
                ],
                'resolve' => function (Stylist $stylist, array $args, GraphQLContext $context): array {
                    /** @var Slot[] $slots */
                    $slots = $context->queryBus->ask(new GetAvailableSlotsQuery(
                        stylistId: $stylist->getId()->toString(),
                        serviceId: $args['serviceId'],
                        date:      $args['date'],
                    ));

                    return ['edges' => $slots];
                },
            ],
        ];
    }
}
