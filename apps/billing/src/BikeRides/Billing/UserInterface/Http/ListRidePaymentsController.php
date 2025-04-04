<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\UserInterface\Http;

use App\BikeRides\Billing\Application\Query\GetRidePaymentByRideId;
use App\BikeRides\Billing\Application\Query\ListRidePayments;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/ride-payment', name: 'ride-payment:list', methods: ['GET'])]
final class ListRidePaymentsController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        ListRidePayments $listRidePayments,
        GetRidePaymentByRideId $getRidePaymentByRideId,
        #[MapQueryParameter(name: 'ride_id')]
        ?string $rideId = null,
    ): JsonResponse {
        $embeddedRidePayments = \array_map(
            static fn ($ridePayment) => [
                'ride_payment_id' => $ridePayment['ride_payment_id'],
                'ride_id' => $ridePayment['ride_id'],
                'total_price' => $ridePayment['total_price']->toArray(),
                'price_per_minute' => $ridePayment['price_per_minute']->toArray(),
                'initiated_at' => $ridePayment['initiated_at']->getTimestamp(),
                'captured_at' => $ridePayment['captured_at']?->getTimestamp(),
                'external_payment_ref' => $ridePayment['external_payment_ref'],
            ],
            null !== $rideId
                ? \array_filter([$getRidePaymentByRideId->query($rideId)])
                : $listRidePayments->query(),
        );

        return new JsonResponse(
            [
                '_links' => [
                    'self' => [
                        'href' => $urlGenerator->generate('ride-payment:list'),
                        'method' => 'GET',
                    ],
                ],
                '_embedded' => [
                    'ride-payment' => $embeddedRidePayments,
                ],
                'total' => \count($embeddedRidePayments),
            ],
            headers: ['Content-Type' => 'application/hal+json'],
        );
    }
}
