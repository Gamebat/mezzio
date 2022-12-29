<?php

namespace Sync\Laminas;

use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyCommand extends Command
{
    protected static $defaultDescription = 'Show current time';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = Carbon::now()->format('H:i (m.Y)');
        $output->writeln("Now time: ". $date);
        return Command::SUCCESS;
    }
}
