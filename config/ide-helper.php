<?php

return [
    'filename' => '_ide_helper.php',
    'meta_filename' => '.phpstorm.meta.php',

    'force_fqn' => false,
    'include_fluent' => true,
    'include_helpers' => true,
    'include_class_docblocks' => false,
    'include_factory_builders' => false,
    'model_camel_case_properties' => false,
    'write_eloquent_model_mixins' => false,
    'write_model_magic_where' => true,
    'write_model_external_builder_methods' => true,
    'write_model_relation_count_properties' => true,

    'helper_files' => [
        base_path() . '/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
    ],
    'model_locations' => [
        'app',
    ],
    'type_overrides' => [
        'integer' => 'int',
        'boolean' => 'bool',
    ],

    'additional_relation_types' => [],
    'custom_db_types' => [],
    'ignored_models' => [],
    'interfaces' => [],
    'magic' => [],
    'extra' => [
        'Eloquent' => ['Illuminate\Database\Eloquent\Builder', 'Illuminate\Database\Query\Builder'],
        'Session' => ['Illuminate\Session\Store'],
    ],
];
