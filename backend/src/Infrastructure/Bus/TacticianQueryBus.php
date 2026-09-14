<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\QueryBus;
use League\Tactician\CommandBus as TacticianBus;

final class TacticianQueryBus implements QueryBus
{
    public function __construct(
        private readonly TacticianBus $bus,
    ) {}

    public function ask(object $query): mixed
    {
        return $this->bus->handle($query);
    }
}
