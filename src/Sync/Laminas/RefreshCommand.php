<?php

namespace Sync\Laminas;

use Laminas\Cli\Command\AbstractParamAwareCommand;
use Laminas\Cli\Input\IntParam;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Producers\RefreshProducer;
use Sync\Workers\RefreshWorker;

class RefreshCommand extends AbstractParamAwareCommand
{
    /**
     * @var string
     */
    protected static $defaultDescription = 'Refresh expiring tokens';

    protected function configure() : void
    {
        $this->addParam(
            (new IntParam('hours'))
            ->setShortcut('-t')
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hours = $input->getParam('hours');

        /*(new RefreshTokens($hours))->refresh();*/
        /*(new RefreshWorker(new BeanstalkConfig(null, true)))->execute($input, $output)*/;
        /*$output->writeln('Hours: ' . $hours);*/

        $output->writeln((new RefreshProducer())->produce($hours));

        return Command::SUCCESS;
    }
}