<?php

use PhpCsFixer\Config;

$config = new Config();

$finder = (new PhpCsFixer\Finder())
    ->notName('*.blade.php')
    ->notName('_ide_helper.php')
    ->notPath('bootstrap/cache')
    ->notPath('node_modules')
    ->notPath('storage')
    ->in(dirname(__DIR__, 2))
;

$config->setFinder($finder)
    ->setUsingCache(false)
    ->setLineEnding("\n")
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,

        // Symfony Overrides
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'php_unit_method_casing' => false,

        // PhpCsFixer Overrides
        'ordered_class_elements' => ['order' => ['use_trait']],
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_add_missing_param_annotation' => false,
        'phpdoc_types_order' => [
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'always_last',
        ],

        // Other Rules
        'backtick_to_shell_exec' => true,
        'comment_to_phpdoc' => true,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'mb_str_functions' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'phpdoc_order' => true,
        'static_lambda' => true,

        // New PHP Functionality
        'declare_strict_types' => false,
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => ['arrays', 'arguments'], // TODO: Add 'parameters' once we drop PHP 7.4
        ],
    ])
;

return $config;
