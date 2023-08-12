<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Ui\Http;

use App\BikeRides\Billing\Application\Query\ListRidePaymentsByRideId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/billing/ride-payment', name: 'billing:ride-payment:list', methods: ['GET'])]
final class ListRidePaymentsController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        ListRidePaymentsByRideId $listRidePaymentsByRideId,
    ): JsonResponse {
        if (! $request->query->has('ride-id')) {
            throw new \RuntimeException('Not implemented');
        }

        $rideId = $request->query->get('ride-id');
        $ridePayments = $listRidePaymentsByRideId->query($rideId);

        $embeddedRidePayments = \array_map(
            static fn (array $ridePayment) => [
                'ride_payment_id' => $ridePayment['ride_payment_id'],
                'ride_id' => $ridePayment['ride_id'],
                'total_price' => $ridePayment['total_price']->jsonSerialize(),
                'price_per_minute' => $ridePayment['price_per_minute']->jsonSerialize(),
                'initiated_at' => $ridePayment['initiated_at']->getTimestamp(),
            ],
            $ridePayments,
        );

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
