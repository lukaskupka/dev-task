<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Queries\Barbershop;

use App\Application\Barbershop\Query\GetBusinessBySlug\GetBusinessBySlugQuery;
use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\UserInterface\GraphQL\GraphQLContext;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

final class BusinessBySlugQuery extends Query
{
    protected $attributes = [
        'name'        => 'businessBySlug',
        'description' => 'Fetch a barbershop business by slug',
    ];

    public function type(): Type
    {
        return GraphQL::type('BarbershopBusiness');
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [
            'slug' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'The URL slug of the business',
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    public function resolve(mixed $root, array $args, GraphQLContext $context): ?Business
    {
        try {
            /** @var Business */
            return $context->queryBus->ask(new GetBusinessBySlugQuery($args['slug']));
        } catch (NotFoundException) {
            return null;
        }
    }
}
