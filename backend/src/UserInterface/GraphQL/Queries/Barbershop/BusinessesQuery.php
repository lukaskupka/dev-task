<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Queries\Barbershop;

use App\Application\Barbershop\Query\GetBusinesses\GetBusinessesQuery;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

final class BusinessesQuery extends Query
{
    protected $attributes = [
        'name'        => 'businesses',
        'description' => 'Fetch all barbershop businesses',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type('BarbershopBusinessConnection'));
    }

    public function resolve(mixed $root, array $args, GraphQLContext $context): array
    {
        $businesses = $context->queryBus->ask(new GetBusinessesQuery());
        return ['edges' => $businesses];
    }
}
