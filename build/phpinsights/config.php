<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenFinalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Code;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\NoSilencedErrorsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ArbitraryParenthesesSpacingSniff;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\RequireYodaComparisonSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;

return [
    'preset' => 'laravel',
    'ide' => null,

    'exclude' => [
    ],

    'add' => [
        Classes::class => [
            ForbiddenFinalClasses::class,
        ],
        Code::class => [
            RequireYodaComparisonSniff::class,
        ],
    ],

    'remove' => [
        // Default Config
        DeclareStrictTypesSniff::class,
        DisallowMixedTypeHintSniff::class,
        ForbiddenDefineFunctions::class,
        ForbiddenNormalClasses::class,
        ForbiddenTraits::class,
        ParameterTypeHintSniff::class,
        PropertyTypeHintSniff::class,
        ReturnTypeHintSniff::class,

        // Manual Overrides
        DisallowShortTernaryOperatorSniff::class,
        DisallowYodaComparisonSniff::class,
        OrderedClassElementsFixer::class,
        SpaceAfterNotSniff::class,
    ],

    'config' => [
        ArbitraryParenthesesSpacingSniff::class => [
            'ignoreNewlines' => true,
        ],
        LineLengthSniff::class => [
            'lineLimit' => 100,
            'absoluteLineLimit' => 110,
        ],
        NoSilencedErrorsSniff::class => [
            'exclude' => [
                'app/FileManager/FileManager.php',
            ],
        ],
        UnusedParameterSniff::class => [
            'exclude' => [
                'app/Rules/Domain.php',
                'app/Rules/NoComma.php',
                'app/Rules/NoColon.php',
                'app/Rules/NoWhitespace.php',
            ],
        ],
        UnusedVariableSniff::class => [
            'exclude' => [
                'app/FileManager/FileManager.php',
            ],
        ],
    ],

    'requirements' => [
        // 'min-quality' => 0,
        // 'min-complexity' => 0,
        // 'min-architecture' => 0,
        // 'min-style' => 0,
        // 'disable-security-check' => false,
    ],

    'threads' => null,
];
