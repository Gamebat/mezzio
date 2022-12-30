<?php

namespace Sync\Laminas;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Workers\TimeWorker;

class MyCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultDescription = 'Show current time';

    /** Запуск воркера из консоли
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        (new TimeWorker(new BeanstalkConfig(null, true)))->execute($input, $output);
        return Command::SUCCESS;
    }
}
