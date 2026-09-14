<?php

declare(strict_types=1);

namespace App\Infrastructure\Barbershop\Repository;

use App\Domain\Barbershop\Entity\Service;
use App\Domain\Barbershop\Exception\NotFoundException;
use App\Domain\Barbershop\Repository\ServiceRepositoryInterface;
use App\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineServiceRepository implements ServiceRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function getById(Uuid $id): Service
    {
        return $this->em->find(Service::class, $id)
            ?? throw new NotFoundException("Service {$id} not found");
    }

    public function save(Service $service): void
    {
        $this->em->persist($service);
        $this->em->flush();
    }
}
