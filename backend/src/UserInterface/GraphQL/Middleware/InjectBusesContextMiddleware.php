<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Middleware;

use App\Application\CommandBus;
use App\Application\QueryBus;
use App\UserInterface\GraphQL\GraphQLContext;
use Closure;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Type\Schema;
use Rebing\GraphQL\Support\ExecutionMiddleware\AbstractExecutionMiddleware;
use Rebing\GraphQL\Support\OperationParams;

final class InjectBusesContextMiddleware extends AbstractExecutionMiddleware
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function handle(string $schemaName, Schema $schema, OperationParams $params, $rootValue, $contextValue, Closure $next): ExecutionResult
    {
        return $next($schemaName, $schema, $params, $rootValue, new GraphQLContext(
            commandBus: $this->commandBus,
            queryBus:   $this->queryBus,
        ));
    }
}
