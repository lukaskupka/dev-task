<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetStylist;

use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Repository\StylistRepositoryInterface;
use App\Domain\ValueObject\UuidFactory;

final class GetStylistQueryHandler
{
    public function __construct(
        private readonly StylistRepositoryInterface $stylistRepository,
        private readonly UuidFactory $uuidFactory,
    ) {}

    public function handle(GetStylistQuery $query): Stylist
    {
        return $this->stylistRepository->getById(
            $this->uuidFactory->fromString($query->id),
        );
    }
}
