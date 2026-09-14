<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\Domain\ValueObject\Uuid;

interface ServiceRepositoryInterface
{
    /** @throws NotFoundException */
    public function getById(Uuid $id): Service;

    public function save(Service $service): void;
}
