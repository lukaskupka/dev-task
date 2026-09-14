<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueObject;

use App\Domain\ValueObject\Uuid as UuidInterface;
use App\Domain\ValueObject\UuidFactory as UuidFactoryInterface;

final class UuidFactory implements UuidFactoryInterface
{
    public function generate(): UuidInterface
    {
        return Uuid::generate();
    }

    public function fromString(string $value): UuidInterface
    {
        return Uuid::fromString($value);
    }
}
