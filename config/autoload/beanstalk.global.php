<?php

return [
    'beanstalk' => [
        'producer' => [
            'host' => 'application-beanstalkd',
            'port' => 11300,
            'timeout' => 10,
        ],
        'worker' => [
            'host' => '127.0.0.1',
            'port' => 11300,
            'timeout' => 10,
        ]

    ]
];