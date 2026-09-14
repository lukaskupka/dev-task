<?php

declare(strict_types=1);

namespace App\Infrastructure\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Stylist;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\Domain\Barbershop\Repository\StylistRepositoryInterface;
use App\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineStylistRepository implements StylistRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function getById(Uuid $id): Stylist
    {
        return $this->em->find(Stylist::class, $id)
            ?? throw new NotFoundException("Stylist {$id} not found");
    }

    public function save(Stylist $stylist): void
    {
        $this->em->persist($stylist);
        $this->em->flush();
    }
}
