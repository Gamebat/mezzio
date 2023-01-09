<?php

namespace Sync\Laminas;

use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;

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
        if($isSli) {
            $this->connection = WorkerConnection::instance();
        } else if ($container) {
            $this->connection = ProducerConnection::instance($container);
        } else {
            $this->connection = WorkerConnection::instance();
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
