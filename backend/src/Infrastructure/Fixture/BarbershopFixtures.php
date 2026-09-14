<?php

declare(strict_types=1);

namespace App\Infrastructure\Fixture;

use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Entity\OpeningHours;
use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Enum\DayOfWeek;
use App\Infrastructure\ValueObject\Uuid;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BarbershopFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // --- Business 1: Gentlemen's Cut ---

        $business1 = new Business(
            Uuid::fromString('11111111-1111-1111-1111-111111111111'),
            "Gentlemen's Cut",
            'gentlemens-cut',
        );

        // Mon–Fri 09:00–18:00, Sat 09:00–14:00, closed Sunday
        foreach ([
            [DayOfWeek::Monday,    '11111111-0000-0000-0001-000000000001', '09:00', '18:00'],
            [DayOfWeek::Tuesday,   '11111111-0000-0000-0001-000000000002', '09:00', '18:00'],
            [DayOfWeek::Wednesday, '11111111-0000-0000-0001-000000000003', '09:00', '18:00'],
            [DayOfWeek::Thursday,  '11111111-0000-0000-0001-000000000004', '09:00', '18:00'],
            [DayOfWeek::Friday,    '11111111-0000-0000-0001-000000000005', '09:00', '18:00'],
            [DayOfWeek::Saturday,  '11111111-0000-0000-0001-000000000006', '09:00', '14:00'],
        ] as [$day, $id, $from, $to]) {
            $business1->addOpeningHours(new OpeningHours(Uuid::fromString($id), $day, $from, $to));
        }

        $service1a = new Service(
            Uuid::fromString('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
            'Classic Haircut',
            30,
            350.0,
            'CZK',
        );
        $service1b = new Service(
            Uuid::fromString('aaaaaaaa-aaaa-aaaa-aaaa-bbbbbbbbbbbb'),
            'Beard Trim',
            20,
            200.0,
            'CZK',
        );
        $service1c = new Service(
            Uuid::fromString('aaaaaaaa-aaaa-aaaa-aaaa-cccccccccccc'),
            'Haircut & Beard',
            50,
            500.0,
            'CZK',
        );

        $stylist1a = new Stylist(
            Uuid::fromString('bbbbbbbb-bbbb-bbbb-bbbb-aaaaaaaaaaaa'),
            'Tomáš Novák',
        );
        $stylist1b = new Stylist(
            Uuid::fromString('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb'),
            'Martin Dvořák',
        );

        $business1->addService($service1a);
        $business1->addService($service1b);
        $business1->addService($service1c);
        $business1->addStylist($stylist1a);
        $business1->addStylist($stylist1b);

        $manager->persist($business1);

        // --- Business 2: Luxury Barber Studio ---

        $business2 = new Business(
            Uuid::fromString('22222222-2222-2222-2222-222222222222'),
            'Luxury Barber Studio',
            'luxury-barber-studio',
        );

        // Tue–Sat 10:00–20:00, closed Mon and Sun
        foreach ([
            [DayOfWeek::Tuesday,   '22222222-0000-0000-0001-000000000002', '10:00', '20:00'],
            [DayOfWeek::Wednesday, '22222222-0000-0000-0001-000000000003', '10:00', '20:00'],
            [DayOfWeek::Thursday,  '22222222-0000-0000-0001-000000000004', '10:00', '20:00'],
            [DayOfWeek::Friday,    '22222222-0000-0000-0001-000000000005', '10:00', '20:00'],
            [DayOfWeek::Saturday,  '22222222-0000-0000-0001-000000000006', '10:00', '20:00'],
        ] as [$day, $id, $from, $to]) {
            $business2->addOpeningHours(new OpeningHours(Uuid::fromString($id), $day, $from, $to));
        }

        $service2a = new Service(
            Uuid::fromString('cccccccc-cccc-cccc-cccc-aaaaaaaaaaaa'),
            'Premium Haircut',
            45,
            600.0,
            'CZK',
        );
        $service2b = new Service(
            Uuid::fromString('cccccccc-cccc-cccc-cccc-bbbbbbbbbbbb'),
            'Hot Towel Shave',
            40,
            450.0,
            'CZK',
        );

        $stylist2a = new Stylist(
            Uuid::fromString('dddddddd-dddd-dddd-dddd-aaaaaaaaaaaa'),
            'Jakub Procházka',
        );

        $business2->addService($service2a);
        $business2->addService($service2b);
        $business2->addStylist($stylist2a);

        $manager->persist($business2);

        $manager->flush();
    }
}
