<?php

namespace Sync\Laminas;

use Laminas\Cli\Command\AbstractParamAwareCommand;
use Laminas\Cli\Input\IntParam;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Producers\RefreshProducer;

class RefreshCommand extends AbstractParamAwareCommand
{
    /**
     * @var string
     */
    protected static $defaultDescription = 'Refresh expiring tokens';

    /** Конфигурирование команды
     * @return void
     */
    protected function configure() : void
    {
        $this->addParam(
            (new IntParam('hours'))
            ->setShortcut('-t')
        );
    }

    /** Функции вызываемые командой
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hours = $input->getParam('hours');
        $output->writeln((new RefreshProducer())->produce($hours));

        return Command::SUCCESS;
    }
}