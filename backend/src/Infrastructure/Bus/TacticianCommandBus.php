<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\CommandBus;
use App\Application\CommandResult;
use League\Tactician\CommandBus as TacticianBus;

final class TacticianCommandBus implements CommandBus
{
    public function __construct(
        private readonly TacticianBus $bus,
    ) {}

    public function dispatch(object $command): CommandResult
    {
        return $this->bus->handle($command);
    }
}
