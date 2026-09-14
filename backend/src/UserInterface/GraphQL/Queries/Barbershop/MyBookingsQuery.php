<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Queries\Barbershop;

use App\Application\Barbershop\Query\GetMyBookings\GetMyBookingsQuery;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

final class MyBookingsQuery extends Query
{
    protected $attributes = [
        'name'        => 'myBookings',
        'description' => 'Fetch bookings for a stylist',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type('BarbershopBookingConnection'));
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [
            'stylistId' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'The ID of the stylist',
            ],
            'status' => [
                'type'        => GraphQL::type('BarbershopBookingStatus'),
                'description' => 'Filter bookings by status',
            ],
            'first' => ['type' => Type::int()],
            'after' => ['type' => Type::string()],
        ];
    }

    /** @param array<string, mixed> $args */
    public function resolve(mixed $root, array $args, GraphQLContext $context): array
    {
        /** @var \App\Domain\Barbershop\Entity\Booking[] $bookings */
        $bookings = $context->queryBus->ask(new GetMyBookingsQuery(
            stylistId: $args['stylistId'],
            status:    $args['status'] ?? null,
        ));

        return ['edges' => $bookings];
    }
}
