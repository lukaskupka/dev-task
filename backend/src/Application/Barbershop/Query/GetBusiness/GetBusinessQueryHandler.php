<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBusiness;

use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Repository\BusinessRepositoryInterface;
use App\Domain\ValueObject\UuidFactory;

final class GetBusinessQueryHandler
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepository,
        private readonly UuidFactory $uuidFactory,
    ) {}

    public function handle(GetBusinessQuery $query): Business
    {
        return $this->businessRepository->getById(
            $this->uuidFactory->fromString($query->id),
        );
    }
}
