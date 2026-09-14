<?php

declare(strict_types=1);

namespace App\Application\Barbershop\Command\ConfirmBooking;

use App\Application\CommandResult;
use App\Domain\Barbershop\Repository\BookingRepositoryInterface;
use App\Domain\ValueObject\UuidFactory;

final class ConfirmBookingCommandHandler
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly UuidFactory $uuidFactory,
    ) {}

    public function handle(ConfirmBookingCommand $command): CommandResult
    {
        $booking = $this->bookingRepository->getById(
            $this->uuidFactory->fromString($command->bookingId),
        );

        $booking->confirm();
        $this->bookingRepository->save($booking);

        return new CommandResult($booking->getId()->toString());
    }
}
