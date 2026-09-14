<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Booking;
use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Exception\SlotAlreadyBookedException;
use App\Infrastructure\Barbershop\Repository\DoctrineBookingRepository;
use App\Infrastructure\Console\LoadFixturesCommand;
use App\Infrastructure\ValueObject\Uuid;
use App\Infrastructure\ValueObject\UuidFactory;
use DateTimeImmutable;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

final class DoctrineBookingRepositoryTest extends TestCase
{
    private const FIXTURE_STYLIST_ID = 'bbbbbbbb-bbbb-bbbb-bbbb-aaaaaaaaaaaa';
    private const FIXTURE_SERVICE_ID = 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa';

    private EntityManagerInterface $em;
    private DoctrineBookingRepository $repository;
    private Stylist $stylist;
    private Service $service;

    protected function setUp(): void
    {
        $container = $this->bootTestContainer();

        $this->runMigrations($container);
        $this->loadFixtures($container);

        $this->em = $container->getByType(EntityManagerInterface::class);
        $this->repository = $container->getByType(DoctrineBookingRepository::class);

        $this->stylist = $this->em->find(Stylist::class, Uuid::fromString(self::FIXTURE_STYLIST_ID))
            ?? throw new RuntimeException('Fixture stylist not found — did BarbershopFixtures change?');
        $this->service = $this->em->find(Service::class, Uuid::fromString(self::FIXTURE_SERVICE_ID))
            ?? throw new RuntimeException('Fixture service not found — did BarbershopFixtures change?');
    }

    private function bootTestContainer(): Container
    {
        $backendDir = dirname(__DIR__, 5);

        $configurator = new Configurator();
        $configurator->addParameters(['appDir' => $backendDir . '/src']);
        $configurator->setTempDirectory($backendDir . '/temp');
        $configurator->addConfig($backendDir . '/config/config.neon');
        $configurator->addConfig($backendDir . '/config/config.test.neon');

        return $configurator->createContainer();
    }

    private function runMigrations(Container $container): void
    {
        $dependencyFactory = $container->getByType(DependencyFactory::class);
        $tester = new CommandTester(new MigrateCommand($dependencyFactory));
        $tester->execute(['--allow-no-migration' => true], ['interactive' => false]);

        if ($tester->getStatusCode() !== 0) {
            throw new RuntimeException('Running migrations failed: ' . $tester->getDisplay());
        }
    }

    private function loadFixtures(Container $container): void
    {
        $tester = new CommandTester($container->getByType(LoadFixturesCommand::class));
        $tester->execute([], ['interactive' => false]);

        if ($tester->getStatusCode() !== 0) {
            throw new RuntimeException('Loading fixtures failed: ' . $tester->getDisplay());
        }
    }

    private function bookingAt(DateTimeImmutable $startTime, ?Stylist $stylist = null): Booking
    {
        return new Booking(
            (new UuidFactory())->generate(),
            $this->service,
            $stylist ?? $this->stylist,
            $startTime,
            $startTime->modify('+30 minutes'),
            'John Smith',
            'john@example.com',
        );
    }

    public function testSavingSecondBookingForSameStylistAndStartTimeThrowsSlotAlreadyBookedException(): void
    {
        $startTime = new DateTimeImmutable('2026-09-14T10:00:00+00:00');

        $this->repository->save($this->bookingAt($startTime));

        $this->expectException(SlotAlreadyBookedException::class);
        $this->repository->save($this->bookingAt($startTime));
    }

    public function testAllowsBookingSameStylistAtDifferentStartTime(): void
    {
        $this->repository->save($this->bookingAt(new DateTimeImmutable('2026-09-14T10:00:00+00:00')));
        $this->repository->save($this->bookingAt(new DateTimeImmutable('2026-09-14T11:00:00+00:00')));

        $this->assertTrue(true); // no exception was thrown
    }

    public function testAllowsRebookingSlotAfterPriorBookingWasRejected(): void
    {
        $startTime = new DateTimeImmutable('2026-09-14T10:00:00+00:00');

        $firstBooking = $this->bookingAt($startTime);
        $this->repository->save($firstBooking);

        $firstBooking->reject();
        $this->em->flush();

        $this->repository->save($this->bookingAt($startTime));

        $this->assertTrue(true); // no exception was thrown
    }
}
