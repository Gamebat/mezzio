<?php

namespace Sync\Workers;

use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Laminas\BeanstalkConfig;


abstract class BaseWorker extends Command
{
    /**
     * @var Pheanstalk|null
     */
    protected Pheanstalk $connection;

    /**
     * @var string
     */
    protected string $queue = 'times';

    /**
     * @param BeanstalkConfig $beanstalk
     */
    final public function __construct(BeanstalkConfig $beanstalk)
    {
        $this->connection = $beanstalk->getConnection();
        parent::__construct();
    }

    /**  Запуск воркера
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        while ($job = ($this->connection)
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
                $this->handleException($exception, $job);
            }

            $this->connection->delete($job);
        }

        return 0;
    }

    /** Обработка ошибок
     * @param \Throwable $exception
     * @param Job $job
     * @return void
     */
    private function handleException(\Throwable $exception, Job $job): void
    {
        echo "Error Unhandled exception $exception" . PHP_EOL . $job->getData();
    }

    abstract function process($data);
}
