<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetStylist;

final class GetStylistQuery
{
    public function __construct(
        public readonly string $id,
    ) {}
}
