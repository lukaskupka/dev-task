<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

final class PingQuery extends Query
{
    protected $attributes = [
        'name' => 'ping',
        'description' => 'Health check — returns "pong"',
    ];

    public function type(): Type
    {
        return Type::string();
    }

    /** @return array<string, mixed> */
    public function args(): array
    {
        return [];
    }

    /** @param array<string, mixed> $args */
    public function resolve(mixed $root, array $args): string
    {
        return 'pong';
    }
}
