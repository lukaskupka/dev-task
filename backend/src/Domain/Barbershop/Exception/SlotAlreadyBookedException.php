<?php

declare(strict_types=1);

namespace App\Domain\Barbershop\Exception;

final class SlotAlreadyBookedException extends \DomainException
{
    public const ERROR_CODE = 'SLOT_TAKEN';

    public function __construct()
    {
        parent::__construct('This time slot has already been booked. Please choose another one.');
    }
}
