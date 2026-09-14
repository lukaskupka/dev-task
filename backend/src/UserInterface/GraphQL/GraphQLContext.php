<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL;

use App\Application\CommandBus;
use App\Application\QueryBus;

final class GraphQLContext
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus,
    ) {}
}
