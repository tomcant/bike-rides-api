<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Model;

final readonly class RideDuration
{
    private const SECONDS_IN_ONE_MINUTE = 60;

    private function __construct(
        public \DateTimeImmutable $startedAt,
        public \DateTimeImmutable $endedAt,
        public int $minutes,
    ) {
    }

    public static function fromStartAndEnd(\DateTimeImmutable $startedAt, \DateTimeImmutable $endedAt): self
    {
        $durationInSeconds = $endedAt->getTimestamp() - $startedAt->getTimestamp();

        if (0 >= $durationInSeconds) {
            throw new \DomainException('Ride end date/time must be after start date/time');
        }

        $durationInMinutes = (int) \ceil($durationInSeconds / self::SECONDS_IN_ONE_MINUTE);

        return new self($startedAt, $endedAt, $durationInMinutes);
    }

    public function toArray(): array
    {
        return [
            'startedAt' => \datetime_timestamp($this->startedAt),
            'endedAt' => \datetime_timestamp($this->endedAt),
            'minutes' => $this->minutes,
        ];
    }

    public static function fromArray(array $rideDuration): self
    {
        return new self(
            new \DateTimeImmutable($rideDuration['startedAt']),
            new \DateTimeImmutable($rideDuration['endedAt']),
            $rideDuration['minutes'],
        );
    }
}
