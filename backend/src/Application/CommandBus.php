<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\CommandResult;

interface CommandBus
{
    public function dispatch(object $command): CommandResult;
}
