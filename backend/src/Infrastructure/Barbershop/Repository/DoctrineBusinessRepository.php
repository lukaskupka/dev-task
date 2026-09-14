<?php

declare(strict_types=1);

namespace App\Infrastructure\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Business;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\Domain\Barbershop\Repository\BusinessRepositoryInterface;
use App\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineBusinessRepository implements BusinessRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function getById(Uuid $id): Business
    {
        return $this->em->find(Business::class, $id)
            ?? throw new NotFoundException("Business {$id} not found");
    }

    public function getBySlug(string $slug): Business
    {
        return $this->em->getRepository(Business::class)->findOneBy(['slug' => $slug])
            ?? throw new NotFoundException("Business with slug '{$slug}' not found");
    }

    public function save(Business $business): void
    {
        $this->em->persist($business);
        $this->em->flush();
    }
}
