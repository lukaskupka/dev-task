<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutations\Barbershop;

use App\Application\Barbershop\Command\CreateBooking\CreateBookingCommand;
use App\Application\Barbershop\Query\GetStylist\GetStylistQuery;
use App\Domain\Barbershop\Entity\Stylist;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

final class CreateBookingMutation extends Mutation
{
    protected $attributes = [
        'name'        => 'createBooking',
        'description' => 'Create a new barbershop booking',
    ];

    public function type(): Type
    {
        return GraphQL::type('CreateBookingPayload');
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [
            'input' => [
                'type'        => Type::nonNull(GraphQL::type('CreateBookingInput')),
                'description' => 'Input for creating a booking',
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    public function resolve(mixed $root, array $args, GraphQLContext $context): array
    {
        try {
            $input  = $args['input'];
            $context->commandBus->dispatch(new CreateBookingCommand(
                stylistId:       $input['stylistId'],
                serviceId:       $input['serviceId'],
                startTime:       $input['startTime'],
                customerName:    $input['customerName'],
                customerContact: $input['customerContact'],
            ));

            /** @var Stylist $stylist */
            $stylist = $context->queryBus->ask(new GetStylistQuery($input['stylistId']));

            return ['stylist' => $stylist, 'errors' => []];
        } catch (\DomainException $e) {
            return ['stylist' => null, 'errors' => [['field' => null, 'message' => $e->getMessage()]]];
        }
    }
}
