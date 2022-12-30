<?php

namespace Sync\Producers;


use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;
use Sync\Laminas\BeanstalkConfig;

class Producer
{
    public Pheanstalk $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->connection = (new BeanstalkConfig($container))->getConnection();
    }

    public function produce($data): array
    {
        $job = ($this->connection)
            ->useTube('times')
            ->put(json_encode($data));

        return [
            'id' => $job->getId(),
            'data' => $job->getData(),
        ];
    }
}