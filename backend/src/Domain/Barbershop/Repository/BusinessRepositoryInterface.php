<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\Domain\ValueObject\Uuid;

interface BusinessRepositoryInterface
{
    /** @throws NotFoundException */
    public function getById(Uuid $id): Business;

    /** @throws NotFoundException */
    public function getBySlug(string $slug): Business;

    public function save(Business $business): void;
}
