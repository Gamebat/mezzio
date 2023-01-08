<?php

namespace Sync\Laminas;

use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BeanstalkConfig
{
    /** Подключение к серверу очередей
     * @var Pheanstalk|null
     */
    private ?Pheanstalk $connection;

    /** Конфигурация подключения
     * @var array|mixed
     */
    private array $config;

    /**
     * Construct Beanstalk
     */
    public function __construct(?ContainerInterface $container, bool $isSli = false)
    {

        try {
            if($isSli)
            {
                $this->config = (include "./config/autoload/beanstalk.global.php")['beanstalk']['worker'];

            } else if ($container){
                $this->config = (include "./config/autoload/beanstalk.global.php")['beanstalk']['producer'];
                /*$this->config = $container->get('config')['beanstalk']['producer'];*/
            } else {
                $this->config = (include "./config/autoload/beanstalk.global.php")['beanstalk']['worker'];
            }

            $this->connection = Pheanstalk::create(
                $this->config['host'],
                $this->config['port'],
                $this->config['timeout']
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            exit($e->getMessage());
        }
    }

    /** Возвращает подключение к серверу очередей
     * @return Pheanstalk|null
     */
    public function getConnection(): ?Pheanstalk
    {
        return $this->connection;
    }
}
