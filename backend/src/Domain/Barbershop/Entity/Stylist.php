<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Entity;

use App\Domain\ValueObject\Uuid;

class Stylist
{
    private Uuid $id;
    private string $name;
    private ?string $photoUrl;
    private Business $business;

    public function __construct(Uuid $id, string $name, ?string $photoUrl = null)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->photoUrl = $photoUrl;
    }

    public function getId(): Uuid { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPhotoUrl(): ?string { return $this->photoUrl; }
    public function getBusiness(): Business { return $this->business; }

    public function setBusiness(Business $business): void { $this->business = $business; }
}
