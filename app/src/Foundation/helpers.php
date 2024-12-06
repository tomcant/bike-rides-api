<?php

declare(strict_types=1);

if (!\function_exists('datetime_timestamp')) {
    function datetime_timestamp(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s.u O');
    }
}

if (!\function_exists('datetime_optional_timestamp')) {
    function datetime_optional_timestamp(?DateTimeInterface $dateTime): ?string
    {
        return $dateTime?->format('Y-m-d H:i:s.u O');
    }
}

if (!\function_exists('money_from_array')) {
    /** @throws InvalidArgumentException */
    function money_from_array(array $money): Money\Money
    {
        return new Money\Money($money['amount'], new Money\Currency($money['currency']));
    }
}

if (!\function_exists('money_to_string')) {
    function money_to_string(Money\Money $money): string
    {
        $moneyFormatter = new Money\Formatter\IntlMoneyFormatter(
            new NumberFormatter('en_GB', NumberFormatter::CURRENCY),
            new Money\Currencies\ISOCurrencies(),
        );

        return $moneyFormatter->format($money);
    }
}
