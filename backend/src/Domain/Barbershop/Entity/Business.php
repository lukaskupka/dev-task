<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Entity;

use App\Domain\Barbershop\Enum\DayOfWeek;
use App\Domain\ValueObject\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Business
{
    private Uuid $id;
    private string $name;
    private string $slug;

    /** @var Collection<int, Service> */
    private Collection $services;

    /** @var Collection<int, Stylist> */
    private Collection $stylists;

    /** @var Collection<int, OpeningHours> */
    private Collection $openingHours;

    public function __construct(Uuid $id, string $name, string $slug)
    {
        $this->id           = $id;
        $this->name         = $name;
        $this->slug         = $slug;
        $this->services     = new ArrayCollection();
        $this->stylists     = new ArrayCollection();
        $this->openingHours = new ArrayCollection();
    }

    public function getId(): Uuid { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }

    /** @return Collection<int, Service> */
    public function getServices(): Collection { return $this->services; }

    /** @return Collection<int, Stylist> */
    public function getStylists(): Collection { return $this->stylists; }

    public function addService(Service $service): void
    {
        $service->setBusiness($this);
        $this->services->add($service);
    }

    public function addStylist(Stylist $stylist): void
    {
        $stylist->setBusiness($this);
        $this->stylists->add($stylist);
    }

    public function addOpeningHours(OpeningHours $openingHours): void
    {
        $openingHours->setBusiness($this);
        $this->openingHours->add($openingHours);
    }

    public function getOpeningHoursForDay(DayOfWeek $day): ?OpeningHours
    {
        foreach ($this->openingHours as $hours) {
            if ($hours->getDayOfWeek() === $day) {
                return $hours;
            }
        }
        return null;
    }
}
