<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutations\Barbershop;

use App\Application\Barbershop\Command\ConfirmBooking\ConfirmBookingCommand;
use App\Application\Barbershop\Query\GetBooking\GetBookingQuery;
use App\Domain\Barbershop\Entity\Booking;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

final class ConfirmBookingMutation extends Mutation
{
    protected $attributes = [
        'name'        => 'confirmBooking',
        'description' => 'Confirm a barbershop booking',
    ];

    public function type(): Type
    {
        return GraphQL::type('ConfirmBookingPayload');
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [
            'input' => [
                'type'        => Type::nonNull(GraphQL::type('ConfirmBookingInput')),
                'description' => 'Input for confirming a booking',
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    public function resolve(mixed $root, array $args, GraphQLContext $context): array
    {
        try {
            $input  = $args['input'];
            $result = $context->commandBus->dispatch(new ConfirmBookingCommand(
                bookingId: $input['bookingId'],
                stylistId: $input['stylistId'],
            ));

            /** @var Booking $booking */
            $booking = $context->queryBus->ask(new GetBookingQuery($result->aggregateId));

            return ['booking' => $booking, 'errors' => []];
        } catch (\DomainException $e) {
            return ['booking' => null, 'errors' => [['field' => null, 'message' => $e->getMessage()]]];
        }
    }
}
