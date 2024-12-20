<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\UserInterface\Http;

use App\BikeRides\Billing\Application\Query\GetRidePaymentByRideId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/billing/ride-payment', name: 'billing:ride-payment:list', methods: ['GET'])]
final class ListRidePaymentsController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetRidePaymentByRideId $getRidePaymentByRideId,
        #[MapQueryParameter(name: 'ride_id')]
        string $rideId,
    ): JsonResponse {
        $ridePayment = $getRidePaymentByRideId->query($rideId);

        $embeddedRidePayments = null === $ridePayment ? [] : [
            [
                'ride_payment_id' => $ridePayment['ride_payment_id'],
                'ride_id' => $ridePayment['ride_id'],
                'total_price' => $ridePayment['total_price']->jsonSerialize(),
                'price_per_minute' => $ridePayment['price_per_minute']->jsonSerialize(),
                'initiated_at' => $ridePayment['initiated_at']->getTimestamp(),
                'captured_at' => $ridePayment['captured_at']?->getTimestamp(),
                'external_payment_ref' => $ridePayment['external_payment_ref'],
            ],
        ];

        return new JsonResponse(
            [
                '_links' => [
                    'self' => [
                        'href' => $urlGenerator->generate('billing:ride-payment:list'),
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
