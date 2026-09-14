<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

interface UuidFactory
{
    public function generate(): Uuid;

    public function fromString(string $value): Uuid;
}
