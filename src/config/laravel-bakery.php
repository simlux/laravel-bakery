<?php
return [
    'model' => [
        'model_path'      => base_path('packages/simlux/laravel-bakery/src/Models'),
        'repository_path' => base_path('packages/simlux/laravel-bakery/src/Models'),
        'migration_path'  => base_path('packages/simlux/laravel-bakery/src/Models'),
        'factory_path'    => base_path('packages/simlux/laravel-bakery/src/Models'),
        'seeder_path'     => base_path('packages/simlux/laravel-bakery/src/Models'),
    ],
    'view'  => [
        'view_path' => base_path('packages/simlux/laravel-bakery/src/resources/views'),
        'title'     => [
            'tag' => 'h3',
        ],
        'content'   => [
            'container_class' => 'container-fluid',
        ],
        'table'     => [
            'table_classes' => [
                'table',
                'table-sm',
                'table-striped',
                'table-bordered',
                'table-xs',
                'table-hover',
            ],
        ],
    ],
];