<?php

namespace Sync\Producers;


use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;
use Sync\Laminas\BeanstalkConfig;

class Producer
{
    /**
     * @var Pheanstalk|null
     */
    public Pheanstalk $connection;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->connection = (new BeanstalkConfig($container))->getConnection();
    }

    /**
     * Создание очереди
     * @param $data
     * @return array
     */
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
