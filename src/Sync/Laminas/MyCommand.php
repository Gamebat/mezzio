<?php

namespace Sync\Laminas;

use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Producers\Producer;
use Sync\Workers\TimeWorker;

class MyCommand extends Command
{
    protected static $defaultDescription = 'Show current time';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        (new TimeWorker(new BeanstalkConfig(null, true)))->execute($input, $output);
        return Command::SUCCESS;
    }
}
