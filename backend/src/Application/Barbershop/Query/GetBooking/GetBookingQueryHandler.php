<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Query\GetBooking;

use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Repository\BookingRepositoryInterface;
use App\Domain\ValueObject\UuidFactory;

final class GetBookingQueryHandler
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly UuidFactory $uuidFactory,
    ) {}

    public function handle(GetBookingQuery $query): Booking
    {
        return $this->bookingRepository->getById(
            $this->uuidFactory->fromString($query->id),
        );
    }
}
