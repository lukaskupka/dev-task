<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Queries\Barbershop;

use App\Application\Barbershop\Query\GetBusinessBookings\GetBusinessBookingsQuery;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

final class BusinessBookingsQuery extends Query
{
    protected $attributes = [
        'name'        => 'businessBookings',
        'description' => 'Fetch all bookings for a business',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type('BarbershopBookingConnection'));
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [
            'businessId' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'The ID of the business',
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
        $bookings = $context->queryBus->ask(new GetBusinessBookingsQuery(
            businessId: $args['businessId'],
            status:     $args['status'] ?? null,
        ));

        return ['edges' => $bookings];
    }
}
