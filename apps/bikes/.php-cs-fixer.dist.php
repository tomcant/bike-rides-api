<?php

declare(strict_types=1);

use Ergebnis\PhpCsFixer\Config;

$ruleSet = Config\RuleSet\Php84::create()->withRules(
    Config\Rules::fromArray([
        'multiline_string_to_heredoc' => false,
        'phpdoc_line_span' => [
            'const' => 'single',
            'method' => 'single',
            'property' => 'single',
        ],
        'php_unit_method_casing' => [
            'case' => 'snake_case',
        ],
    ]),
);

$config = Config\Factory::fromRuleSet($ruleSet);

$config->getFinder()->in(__DIR__);

return $config;
