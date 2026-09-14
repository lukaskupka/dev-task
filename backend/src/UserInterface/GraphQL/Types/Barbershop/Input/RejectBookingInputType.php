<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Input;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

final class RejectBookingInputType extends InputType
{
    protected $attributes = [
        'name'        => 'RejectBookingInput',
        'description' => 'Input for rejecting a booking',
    ];

    public function fields(): array
    {
        return [
            'bookingId' => ['type' => Type::nonNull(Type::id())],
            'stylistId' => ['type' => Type::nonNull(Type::id())],
            'reason'    => ['type' => Type::string()],
        ];
    }
}
