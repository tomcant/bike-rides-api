<?php declare(strict_types=1);

if (! \function_exists('json_encode_array')) {
    /** @throws JsonException */
    function json_encode_array(mixed $value, int $options = \JSON_THROW_ON_ERROR, int $depth = 512): string
    {
        return \json_encode($value, $options, $depth);
    }
}

if (! \function_exists('json_decode_array')) {
    /** @throws JsonException */
    function json_decode_array(string $json, bool $assoc = true, int $depth = 512, int $options = \JSON_THROW_ON_ERROR): array
    {
        return \json_decode($json, $assoc, $depth, $options) ?? [];
    }
}

if (! \function_exists('datetime_timestamp')) {
    function datetime_timestamp(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s.u O');
    }
}

if (! \function_exists('datetime_optional_timestamp')) {
    function datetime_optional_timestamp(?DateTimeInterface $dateTime): ?string
    {
        return $dateTime?->format('Y-m-d H:i:s.u O');
    }
}

if (! \function_exists('money_from_array')) {
    /** @throws InvalidArgumentException */
    function money_from_array(array $money): Money\Money
    {
        return new Money\Money($money['amount'], new Money\Currency($money['currency']));
    }
}

if (! \function_exists('money_to_string')) {
    function money_to_string(Money\Money $money): string
    {
        $moneyFormatter = new Money\Formatter\IntlMoneyFormatter(
            new NumberFormatter('en_GB', NumberFormatter::CURRENCY),
            new Money\Currencies\ISOCurrencies(),
        );

        return $moneyFormatter->format($money);
    }
}
