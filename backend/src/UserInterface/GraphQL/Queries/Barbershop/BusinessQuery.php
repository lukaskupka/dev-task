<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Queries\Barbershop;

use App\Application\Barbershop\Query\GetBusiness\GetBusinessQuery;
use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

final class BusinessQuery extends Query
{
    protected $attributes = [
        'name'        => 'business',
        'description' => 'Fetch a barbershop business by ID',
    ];

    public function type(): Type
    {
        return GraphQL::type('BarbershopBusiness');
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::id()),
                'description' => 'The ID of the business',
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    public function resolve(mixed $root, array $args, GraphQLContext $context): ?Business
    {
        try {
            /** @var Business */
            return $context->queryBus->ask(new GetBusinessQuery($args['id']));
        } catch (NotFoundException) {
            return null;
        }
    }
}
