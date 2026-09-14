<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueObject;

use App\Domain\ValueObject\Uuid as UuidInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid implements UuidInterface
{
    private function __construct(
        private readonly \Ramsey\Uuid\UuidInterface $uuid,
    ) {}

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4());
    }

    public static function fromString(string $value): self
    {
        return new self(RamseyUuid::fromString($value));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }

    public function equals(UuidInterface $other): bool
    {
        return $this->uuid->toString() === $other->toString();
    }
}
