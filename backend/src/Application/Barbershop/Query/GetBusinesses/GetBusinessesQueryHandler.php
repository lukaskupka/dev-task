<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBusinesses;

use App\Domain\Barbershop\Entity\Business;
use Doctrine\ORM\EntityManagerInterface;

final class GetBusinessesQueryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** @return Business[] */
    public function handle(GetBusinessesQuery $query): array
    {
        return $this->em->getRepository(Business::class)->findAll();
    }
}
