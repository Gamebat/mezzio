<?php

namespace Sync\Workers;

use Pheanstalk\Pheanstalk;

class TimeWorker
{
    protected Pheanstalk $connection;

    protected string $queue = 'times';

    final public function __construct()
    {
        $this->connection = Pheanstalk::create('127.0.0.1');
    }

    public function execute()
    {
        while ($job = $this->connection
        ->watchOnly($this->queue)
        ->ignore(Pheanstalk::DEFAULT_TUBE)
        ->reserve()
        ) {
            try {
                $this->process(json_decode(
                    $job->getData(),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ));
            } catch (\Throwable $exception) {
                exit($exception->getMessage());
            }

            $this->connection->delete($job);
        }
    }

    public function process($data)
    {
        echo $data;
    }
}
