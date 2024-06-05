<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\RefreshBikeLocation;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\BikeId;

final readonly class RefreshBikeLocationCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(string $bikeId)
    {
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'bikeId' => $this->bikeId->toString(),
        ]);
    }
}
