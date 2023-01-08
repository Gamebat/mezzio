<?php

namespace Sync\Producers;

use Carbon\Carbon;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Laminas\BeanstalkConfig;

class RefreshProducer
{
    /**
     * @var Pheanstalk|null
     */
    public Pheanstalk $connection;

    /**
     * @param ContainerInterface $container
     */
    public function __construct()
    {
        $this->connection = (new BeanstalkConfig(null, false))->getConnection();
    }

    /**
     * Создание очереди
     * @param $data
     * @return array
     */
    public function produce($hours)
    {
        try {
            $job = ($this->connection)
                ->useTube('refresh')
                ->put(json_encode($hours));

            return [
                'job id: '. $job->getId(),
                'date: '. Carbon::now(),
                'message: Update tokens',
            ];
        } catch (\Exception $e) {
            die ($e->getMessage());
        }
    }
}
