<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\ValueObject;

use DateTimeImmutable;

final class Slot
{
    public function __construct(
        public readonly DateTimeImmutable $startTime,
        public readonly DateTimeImmutable $endTime,
    ) {}
}
