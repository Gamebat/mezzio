<?php

namespace Sync\Laminas;

use Exception;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final class ProducerConnection
{
    /**
     * @var Pheanstalk|null
     */
    private static ?Pheanstalk $inst = null;

    /**
     * @var array
     */
    private static array $config;

    private function __clone(){}

    private function __wakeup(){}

    /** Создание подключения к Pheanstalk
     * @param ContainerInterface $container
     * @return Pheanstalk
     */
    public static function instance(ContainerInterface $container): Pheanstalk
    {
        try {
            if (self::$inst === null)
            {
                self::$config = $container->get('config')['beanstalk']['producer'];
                self::$inst = Pheanstalk::create(
                    self::$config['host'],
                    self::$config['port'],
                    self::$config['timeout']
                );
            }
            return self::$inst;
        } catch (ContainerExceptionInterface|Exception $e) {
            exit($e->getMessage());
        }
    }

    private function __construct(){}
}
