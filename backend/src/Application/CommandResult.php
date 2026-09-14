<?php

declare(strict_types=1);

namespace App\Application;

final class CommandResult
{
    public function __construct(
        public readonly string $aggregateId,
    ) {}
}
