<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Entity;

use App\Domain\Barbershop\Enum\DayOfWeek;
use App\Domain\ValueObject\Uuid;

class OpeningHours
{
    private Uuid $id;
    private Business $business;
    private DayOfWeek $dayOfWeek;
    private string $openFrom; // HH:MM
    private string $openTo;   // HH:MM

    public function __construct(Uuid $id, DayOfWeek $dayOfWeek, string $openFrom, string $openTo)
    {
        $this->id        = $id;
        $this->dayOfWeek = $dayOfWeek;
        $this->openFrom  = $openFrom;
        $this->openTo    = $openTo;
    }

    public function getId(): Uuid { return $this->id; }
    public function getDayOfWeek(): DayOfWeek { return $this->dayOfWeek; }
    public function getOpenFrom(): string { return $this->openFrom; }
    public function getOpenTo(): string { return $this->openTo; }

    public function setBusiness(Business $business): void { $this->business = $business; }
}
