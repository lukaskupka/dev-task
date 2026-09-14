<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetAvailableSlots;

use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Enum\BookingStatus;
use App\Domain\Barbershop\Enum\DayOfWeek;
use App\Domain\Barbershop\Repository\ServiceRepositoryInterface;
use App\Domain\Barbershop\Repository\StylistRepositoryInterface;
use App\Domain\Barbershop\ValueObject\Slot;
use App\Domain\ValueObject\UuidFactory;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class GetAvailableSlotsQueryHandler
{
    public function __construct(
        private readonly StylistRepositoryInterface $stylistRepository,
        private readonly ServiceRepositoryInterface $serviceRepository,
        private readonly EntityManagerInterface $em,
        private readonly UuidFactory $uuidFactory,
    ) {}

    /** @return Slot[] */
    public function handle(GetAvailableSlotsQuery $query): array
    {
        $stylist = $this->stylistRepository->getById($this->uuidFactory->fromString($query->stylistId));
        $service = $this->serviceRepository->getById($this->uuidFactory->fromString($query->serviceId));

        $business   = $stylist->getBusiness();
        $duration   = $service->getDurationMinutes();
        $dayOfWeek  = DayOfWeek::from((int) (new DateTimeImmutable($query->date))->format('N'));
        $todayHours = $business->getOpeningHoursForDay($dayOfWeek);

        if ($todayHours === null) {
            return [];
        }

        $openFrom = new DateTimeImmutable("{$query->date} {$todayHours->getOpenFrom()}");
        $openTo   = new DateTimeImmutable("{$query->date} {$todayHours->getOpenTo()}");
        $step     = new DateInterval("PT{$duration}M");

        $allSlots  = [];
        $slotStart = $openFrom;
        while (true) {
            $slotEnd = $slotStart->add($step);
            if ($slotEnd > $openTo) {
                break;
            }
            $allSlots[] = new Slot($slotStart, $slotEnd);
            $slotStart  = $slotEnd;
        }

        $dayStart = new DateTimeImmutable("{$query->date} 00:00:00");
        $dayEnd   = new DateTimeImmutable("{$query->date} 23:59:59");

        /** @var Booking[] $bookings */
        $bookings = $this->em->createQueryBuilder()
            ->select('b')
            ->from(Booking::class, 'b')
            ->where('b.stylist = :stylist')
            ->andWhere('b.startTime >= :dayStart')
            ->andWhere('b.startTime <= :dayEnd')
            ->andWhere('b.status != :rejected')
            ->setParameter('stylist', $stylist)
            ->setParameter('dayStart', $dayStart)
            ->setParameter('dayEnd', $dayEnd)
            ->setParameter('rejected', BookingStatus::Rejected->value)
            ->getQuery()
            ->getResult();

        return array_values(array_filter(
            $allSlots,
            static function (Slot $slot) use ($bookings): bool {
                foreach ($bookings as $booking) {
                    if ($booking->getStartTime() < $slot->endTime && $booking->getEndTime() > $slot->startTime) {
                        return false;
                    }
                }
                return true;
            },
        ));
    }
}
