<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetMyBookings;

use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Enum\BookingStatus;
use Doctrine\ORM\EntityManagerInterface;

final class GetMyBookingsQueryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** @return Booking[] */
    public function handle(GetMyBookingsQuery $query): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from(Booking::class, 'b')
            ->join('b.stylist', 's')
            ->where('s.id = :stylistId')
            ->setParameter('stylistId', $query->stylistId);

        if ($query->status !== null) {
            $qb->andWhere('b.status = :status')
               ->setParameter('status', strtolower($query->status));
        }

        /** @var Booking[] */
        return $qb->getQuery()->getResult();
    }
}
