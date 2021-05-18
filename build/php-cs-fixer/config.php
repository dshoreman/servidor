<?php

use PhpCsFixer\Config;

$config = new Config();

$finder = (new PhpCsFixer\Finder())
    ->notName('*.blade.php')
    ->notName('_ide_helper.php')
    ->notPath('bootstrap/cache')
    ->notPath('node_modules')
    ->notPath('storage')
    ->in(dirname(__DIR__, 2));

$config->setFinder($finder)
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
        'php_unit_method_casing' => false,

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
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        'declare_strict_types' => false,

        // Stricter migration overrides
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => ['arrays', 'arguments'], // TODO: Add 'parameters' once we drop PHP 7.4
        ],
    ]);

return $config;
