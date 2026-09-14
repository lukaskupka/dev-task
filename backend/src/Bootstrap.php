<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;

final class Bootstrap
{
    public static function boot(): Container
    {
        $configurator = new Configurator();
        $configurator->setTempDirectory(__DIR__ . '/../temp');
        $configurator->addConfig(__DIR__ . '/../config/config.neon');

        return $configurator->createContainer();
    }
}
