<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

interface Uuid
{
    public function toString(): string;

    public function equals(self $other): bool;

    public function __toString(): string;
}
