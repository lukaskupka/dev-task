<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Types\Barbershop\Input;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

final class CreateBookingInputType extends InputType
{
    protected $attributes = [
        'name'        => 'CreateBookingInput',
        'description' => 'Input for creating a booking',
    ];

    public function fields(): array
    {
        return [
            'stylistId'       => ['type' => Type::nonNull(Type::id())],
            'serviceId'       => ['type' => Type::nonNull(Type::id())],
            'startTime'       => ['type' => Type::nonNull(Type::string())],
            'customerName'    => ['type' => Type::nonNull(Type::string())],
            'customerContact' => ['type' => Type::nonNull(Type::string())],
        ];
    }
}
