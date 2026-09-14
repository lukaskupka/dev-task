<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Entity;

use App\Domain\ValueObject\Uuid;

class Service
{
    private Uuid $id;
    private string $name;
    private int $durationMinutes;
    private float $price;
    private string $currency;
    private Business $business;

    public function __construct(Uuid $id, string $name, int $durationMinutes, float $price, string $currency)
    {
        $this->id              = $id;
        $this->name            = $name;
        $this->durationMinutes = $durationMinutes;
        $this->price           = $price;
        $this->currency        = $currency;
    }

    public function getId(): Uuid { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDurationMinutes(): int { return $this->durationMinutes; }
    public function getPrice(): float { return $this->price; }
    public function getCurrency(): string { return $this->currency; }
    public function getBusiness(): Business { return $this->business; }

    public function setBusiness(Business $business): void { $this->business = $business; }
}
