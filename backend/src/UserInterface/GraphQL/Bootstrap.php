<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL;

use App\Application\CommandBus;
use App\Application\QueryBus;
use App\UserInterface\GraphQL\Config\ArrayRepository;
use App\UserInterface\GraphQL\Middleware\InjectBusesContextMiddleware;
use App\UserInterface\GraphQL\Mutations\Barbershop\ConfirmBookingMutation;
use App\UserInterface\GraphQL\Mutations\Barbershop\CreateBookingMutation;
use App\UserInterface\GraphQL\Mutations\Barbershop\RejectBookingMutation;
use App\UserInterface\GraphQL\Queries\Barbershop\BusinessBookingsQuery;
use App\UserInterface\GraphQL\Queries\Barbershop\BusinessBySlugQuery;
use App\UserInterface\GraphQL\Queries\Barbershop\BusinessesQuery;
use App\UserInterface\GraphQL\Queries\Barbershop\BusinessQuery;
use App\UserInterface\GraphQL\Queries\Barbershop\MyBookingsQuery;
use App\UserInterface\GraphQL\Queries\PingQuery;
use App\UserInterface\GraphQL\Types\Barbershop\BarbershopPageInfoType;
use App\UserInterface\GraphQL\Types\Barbershop\BookingStatusEnumType;
use App\UserInterface\GraphQL\Types\Barbershop\BookingType;
use App\UserInterface\GraphQL\Types\Barbershop\BusinessType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\BookingConnectionType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\BusinessConnectionType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\BusinessEdgeType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\BookingEdgeType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\ServiceConnectionType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\ServiceEdgeType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\SlotConnectionType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\SlotEdgeType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\StylistConnectionType;
use App\UserInterface\GraphQL\Types\Barbershop\Connections\StylistEdgeType;
use App\UserInterface\GraphQL\Types\Barbershop\Input\ConfirmBookingInputType;
use App\UserInterface\GraphQL\Types\Barbershop\Input\CreateBookingInputType;
use App\UserInterface\GraphQL\Types\Barbershop\Input\RejectBookingInputType;
use App\UserInterface\GraphQL\Types\Barbershop\Payload\ConfirmBookingPayloadType;
use App\UserInterface\GraphQL\Types\Barbershop\Payload\CreateBookingPayloadType;
use App\UserInterface\GraphQL\Types\Barbershop\Payload\RejectBookingPayloadType;
use App\UserInterface\GraphQL\Types\Barbershop\ServiceType;
use App\UserInterface\GraphQL\Types\Barbershop\SlotType;
use App\UserInterface\GraphQL\Types\Barbershop\StylistType;
use App\UserInterface\GraphQL\Types\Barbershop\UserErrorType;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Facade;
use Nette\DI\Container as NetteContainer;
use Rebing\GraphQL\GraphQL;

final class Bootstrap
{
    public static function createContainer(NetteContainer $netteContainer): Container
    {
        $container = new Container();
        Container::setInstance($container);
        Facade::setFacadeApplication($container);

        $container->singleton(InjectBusesContextMiddleware::class, static fn() => new InjectBusesContextMiddleware(
            $netteContainer->getByType(CommandBus::class),
            $netteContainer->getByType(QueryBus::class),
        ));

        $config = new ArrayRepository([
            'graphql' => [
                'default_schema' => 'default',
                'schemas' => [
                    'default' => [
                        'query' => [
                            'ping'             => PingQuery::class,
                            'businesses'       => BusinessesQuery::class,
                            'business'         => BusinessQuery::class,
                            'businessBySlug'   => BusinessBySlugQuery::class,
                            'myBookings'       => MyBookingsQuery::class,
                            'businessBookings' => BusinessBookingsQuery::class,
                        ],
                        'mutation' => [
                            'createBooking'  => CreateBookingMutation::class,
                            'confirmBooking' => ConfirmBookingMutation::class,
                            'rejectBooking'  => RejectBookingMutation::class,
                        ],
                        'execution_middleware' => [
                            InjectBusesContextMiddleware::class,
                        ],
                        'types' => [
                            CreateBookingInputType::class,
                            ConfirmBookingInputType::class,
                            RejectBookingInputType::class,
                            BusinessEdgeType::class,
                            BusinessConnectionType::class,
                            BarbershopPageInfoType::class,
                            BookingStatusEnumType::class,
                            UserErrorType::class,
                            ServiceType::class,
                            SlotType::class,
                            StylistType::class,
                            BookingType::class,
                            BusinessType::class,
                            ServiceEdgeType::class,
                            ServiceConnectionType::class,
                            StylistEdgeType::class,
                            StylistConnectionType::class,
                            SlotEdgeType::class,
                            SlotConnectionType::class,
                            BookingEdgeType::class,
                            BookingConnectionType::class,
                            CreateBookingPayloadType::class,
                            ConfirmBookingPayloadType::class,
                            RejectBookingPayloadType::class,
                        ],
                    ],
                ],
                'batching'              => ['enable' => true],
                'headers'               => [],
                'json_encoding_options' => 0,
                'defaultFieldResolver'  => null,
                'execution_middleware'  => [],
                'resolver_middleware_append' => [],
            ],
        ]);

        $container->instance(Repository::class, $config);
        $container->instance(ArrayRepository::class, $config);
        $container->instance('config', $config);

        $container->bind(Pipeline::class, static fn(Container $c) => new Pipeline($c));

        $container->singleton(ExceptionHandler::class, TracyExceptionHandler::class);

        $container->singleton(GraphQL::class, static fn(Container $c) => new GraphQL($c, $config));
        $container->alias(GraphQL::class, 'graphql');

        return $container;
    }
}
