<?php

namespace Sync\Producers;

use Carbon\Carbon;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;
use Sync\Laminas\BeanstalkConfig;

class RefreshProducer
{
    /**
     * @var Pheanstalk
     */
    public Pheanstalk $connection;

    public function __construct()
    {
        $this->connection = (new BeanstalkConfig(null, false))->getConnection();
    }

    /**
     * Создание очереди
     * @param $data
     * @return array
     */
    public function produce(int $hours): array
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
