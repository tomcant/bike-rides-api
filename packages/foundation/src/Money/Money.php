<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Money;

use Money\Currency as BaseCurrency;
use Money\Money as BaseMoney;

/** @method static Money GBP(int $amount) */
final readonly class Money
{
    private BaseMoney $money;

    public function __construct(
        public int|string $amount,
        public Currency $currency,
    ) {
        $this->money = new BaseMoney($amount, new BaseCurrency($currency->name));
    }

    public static function from(int|string $amount, Currency|string $currency): self
    {
        if (!$currency instanceof Currency) {
            return new self($amount, Currency::from($currency));
        }

        return new self($amount, $currency);
    }

    /** @param array<mixed> $money */
    public static function fromArray(array $money): self
    {
        if (!\array_key_exists('amount', $money) || !\array_key_exists('currency', $money)) {
            throw new \InvalidArgumentException('Invalid input provided');
        }

        return self::from($money['amount'], $money['currency']);
    }

    /** @return array{amount: string, currency: string} */
    public function toArray(): array
    {
        return $this->money->jsonSerialize();
    }

    public function toString(): string
    {
         $moneyFormatter = new \Money\Formatter\IntlMoneyFormatter(
             new \NumberFormatter('en_GB', \NumberFormatter::CURRENCY),
             new \Money\Currencies\ISOCurrencies(),
         );

         return $moneyFormatter->format($this->money);
    }

    public function multiply(int|string $multiplier): self
    {
        return self::fromBaseMoney($this->money->multiply($multiplier));
    }

    private static function fromBaseMoney(BaseMoney $money): self
    {
        return self::from($money->getAmount(), $money->getCurrency()->getCode());
    }

    /** @phpstan-ignore-next-line */
    public static function __callStatic(string $method, array $arguments): self
    {
        return self::from($arguments[0], currency: $method);
    }
}
