<?php declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use App\BikeRides\Shared\Domain\Model\BikeId;

final class BikeNotFound extends \DomainException
{
    public function __construct(BikeId $id)
    {
        parent::__construct(\sprintf("Unable to find bike with id '%s'", $id->toString()));
    }
}
