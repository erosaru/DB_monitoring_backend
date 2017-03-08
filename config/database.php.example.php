<?php
return [

    'default' => 'local',
    'connections' => [
        'local' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'example',
            'username'  => 'example',
            'password'  => 'example',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]
    ],
];