<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBusinessBookings;

use App\Domain\Barbershop\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

final class GetBusinessBookingsQueryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** @return Booking[] */
    public function handle(GetBusinessBookingsQuery $query): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from(Booking::class, 'b')
            ->join('b.stylist', 's')
            ->join('s.business', 'bus')
            ->where('bus.id = :businessId')
            ->setParameter('businessId', $query->businessId)
            ->orderBy('b.startTime', 'ASC');

        if ($query->status !== null) {
            $qb->andWhere('b.status = :status')
               ->setParameter('status', strtolower($query->status));
        }

        /** @var Booking[] */
        return $qb->getQuery()->getResult();
    }
}
