<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Model;

use BikeRides\Foundation\Timestamp;

final readonly class RideDuration
{
    private const int SECONDS_IN_ONE_MINUTE = 60;

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

    /**
     * @return array{
     *   startedAt: string,
     *   endedAt: string,
     *   minutes: int,
     * }
     */
    public function toArray(): array
    {
        return [
            'startedAt' => Timestamp::format($this->startedAt),
            'endedAt' => Timestamp::format($this->endedAt),
            'minutes' => $this->minutes,
        ];
    }

    /**
     * @param array{
     *   startedAt: string,
     *   endedAt: string,
     *   minutes: int,
     * } $rideDuration
     */
    public static function fromArray(array $rideDuration): self
    {
        return new self(
            Timestamp::from($rideDuration['startedAt']),
            Timestamp::from($rideDuration['endedAt']),
            $rideDuration['minutes'],
        );
    }
}
