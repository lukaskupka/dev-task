<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\Domain\ValueObject\Uuid;

interface StylistRepositoryInterface
{
    /** @throws NotFoundException */
    public function getById(Uuid $id): Stylist;

    public function save(Stylist $stylist): void;
}
