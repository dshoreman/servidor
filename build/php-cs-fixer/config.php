<?php

$finder = (new PhpCsFixer\Finder)
    ->notName('*.blade.php')
    ->notPath('bootstrap/cache')
    ->notPath('storage')
    ->in(dirname(__DIR__, 2));

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setUsingCache(false)
    ->setLineEnding("\n")
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,

        // Symfony Overrides
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'new_with_braces' => false,
        'no_short_bool_cast' => false,

        // Other Rules
        'array_indentation' => true,
        'backtick_to_shell_exec' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'mb_str_functions' => true,
        'multiline_comment_opening_closing' => true,
        'no_useless_else' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'phpdoc_order' => true,

        // New PHP Functionality
        '@PHP73Migration' => true,
        '@PHP71Migration:risky' => true,
        'declare_strict_types' => false,
    ]);

// vim: ft=php
