<?php

return [
    'beanstalk' => [
        'producer' => [
            'host' => 'application-beanstalkd',
            'port' => 11300,
            'timeout' => 10,
        ],
        'worker' => [
            'host' => 'localhost',
            'port' => 11300,
            'timeout' => 10,
        ]

    ]
];