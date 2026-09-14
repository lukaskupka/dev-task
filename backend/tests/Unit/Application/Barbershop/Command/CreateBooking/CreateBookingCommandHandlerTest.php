<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Barbershop\Command\CreateBooking;

use App\Application\Barbershop\Command\CreateBooking\CreateBookingCommand;
use App\Application\Barbershop\Command\CreateBooking\CreateBookingCommandHandler;
use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Exception\SlotAlreadyBookedException;
use App\Domain\Barbershop\Repository\BookingRepositoryInterface;
use App\Domain\ValueObject\UuidFactory as UuidFactoryInterface;
use App\Infrastructure\ValueObject\UuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CreateBookingCommandHandlerTest extends TestCase
{
    private UuidFactoryInterface $uuidFactory;
    private Service $service;
    private Stylist $stylist;

    protected function setUp(): void
    {
        $this->uuidFactory = new UuidFactory();
        $this->service = new Service($this->uuidFactory->generate(), 'Haircut', 30, 250.0, 'CZK');
        $this->stylist = new Stylist($this->uuidFactory->generate(), 'Jane Doe');
    }

    private function createHandler(
        BookingRepositoryInterface $bookingRepository,
        Service|false|null $service = false,
        Stylist|false|null $stylist = false,
    ): CreateBookingCommandHandler {
        $service = $service === false ? $this->service : $service;
        $stylist = $stylist === false ? $this->stylist : $stylist;

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('find')->willReturnCallback(
            fn (string $class) => match ($class) {
                Service::class => $service,
                Stylist::class => $stylist,
                default => null,
            }
        );

        return new CreateBookingCommandHandler($bookingRepository, $em, $this->uuidFactory);
    }

    private function createCommand(): CreateBookingCommand
    {
        return new CreateBookingCommand(
            stylistId: $this->stylist->getId()->toString(),
            serviceId: $this->service->getId()->toString(),
            startTime: '2026-09-14T10:00:00+00:00',
            customerName: 'John Smith',
            customerContact: 'john@example.com',
        );
    }

    public function testSavesBookingAndReturnsItsId(): void
    {
        $savedBooking = null;

        $bookingRepository = $this->createMock(BookingRepositoryInterface::class);
        $bookingRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Booking $booking) use (&$savedBooking) {
                $savedBooking = $booking;
                return true;
            }));

        $handler = $this->createHandler($bookingRepository);
        $result = $handler->handle($this->createCommand());

        $this->assertNotNull($savedBooking);
        $this->assertSame($savedBooking->getId()->toString(), $result->aggregateId);
        $this->assertSame($this->service, $savedBooking->getService());
        $this->assertSame($this->stylist, $savedBooking->getStylist());
        $this->assertSame('John Smith', $savedBooking->getCustomerName());
        $this->assertSame('john@example.com', $savedBooking->getCustomerContact());
    }

    public function testComputesEndTimeFromServiceDuration(): void
    {
        $bookingRepository = $this->createMock(BookingRepositoryInterface::class);
        $bookingRepository->method('save')->willReturnCallback(function (Booking $booking) {
            $this->assertSame(
                $booking->getStartTime()->modify('+30 minutes')->format(DATE_ATOM),
                $booking->getEndTime()->format(DATE_ATOM),
            );
        });

        $handler = $this->createHandler($bookingRepository);
        $handler->handle($this->createCommand());
    }

    public function testThrowsWhenServiceNotFound(): void
    {
        $bookingRepository = $this->createMock(BookingRepositoryInterface::class);
        $bookingRepository->expects($this->never())->method('save');

        $handler = $this->createHandler($bookingRepository, service: null);

        $this->expectException(DomainException::class);
        $handler->handle($this->createCommand());
    }

    public function testThrowsWhenStylistNotFound(): void
    {
        $bookingRepository = $this->createMock(BookingRepositoryInterface::class);
        $bookingRepository->expects($this->never())->method('save');

        $handler = $this->createHandler($bookingRepository, stylist: null);

        $this->expectException(DomainException::class);
        $handler->handle($this->createCommand());
    }

    public function testPropagatesSlotAlreadyBookedExceptionFromRepository(): void
    {
        $bookingRepository = $this->createMock(BookingRepositoryInterface::class);
        $bookingRepository->method('save')->willThrowException(new SlotAlreadyBookedException());

        $handler = $this->createHandler($bookingRepository);

        $this->expectException(SlotAlreadyBookedException::class);
        $handler->handle($this->createCommand());
    }
}
