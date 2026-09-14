<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Command\CreateBooking;

use App\Application\CommandResult;
use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Repository\BookingRepositoryInterface;
use App\Domain\ValueObject\UuidFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;

final class CreateBookingCommandHandler
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly EntityManagerInterface $em,
        private readonly UuidFactory $uuidFactory,
    ) {}

    public function handle(CreateBookingCommand $command): CommandResult
    {
        $service = $this->em->find(Service::class, $this->uuidFactory->fromString($command->serviceId))
            ?? throw new DomainException("Service {$command->serviceId} not found");

        $stylist = $this->em->find(Stylist::class, $this->uuidFactory->fromString($command->stylistId))
            ?? throw new DomainException("Stylist {$command->stylistId} not found");

        $start = new DateTimeImmutable($command->startTime);
        $end   = $start->modify("+{$service->getDurationMinutes()} minutes");

        $booking = new Booking(
            $this->uuidFactory->generate(),
            $service,
            $stylist,
            $start,
            $end,
            $command->customerName,
            $command->customerContact,
        );

        $this->bookingRepository->save($booking);

        return new CommandResult($booking->getId()->toString());
    }
}
