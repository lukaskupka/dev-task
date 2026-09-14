<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Throwable;

final class NullExceptionHandler implements ExceptionHandler
{
    public function report(Throwable $e): void
    {
    }

    public function shouldReport(Throwable $e): bool
    {
        return false;
    }

    public function render($request, Throwable $e): never
    {
        throw $e;
    }

    public function renderForConsole($output, Throwable $e): void
    {
    }
}
