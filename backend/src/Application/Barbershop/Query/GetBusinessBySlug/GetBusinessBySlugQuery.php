<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBusinessBySlug;

final class GetBusinessBySlugQuery
{
    public function __construct(
        public readonly string $slug,
    ) {}
}
