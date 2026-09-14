<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBusinessBySlug;

use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Repository\BusinessRepositoryInterface;

final class GetBusinessBySlugQueryHandler
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepository,
    ) {}

    public function handle(GetBusinessBySlugQuery $query): Business
    {
        return $this->businessRepository->getBySlug($query->slug);
    }
}
