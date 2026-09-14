<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Command\RejectBooking;

use App\Application\CommandResult;
use App\Domain\Barbershop\Repository\BookingRepositoryInterface;
use App\Domain\ValueObject\UuidFactory;

final class RejectBookingCommandHandler
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly UuidFactory $uuidFactory,
    ) {}

    public function handle(RejectBookingCommand $command): CommandResult
    {
        $booking = $this->bookingRepository->getById(
            $this->uuidFactory->fromString($command->bookingId),
        );

        $booking->reject();
        $this->bookingRepository->save($booking);

        return new CommandResult($booking->getId()->toString());
    }
}
