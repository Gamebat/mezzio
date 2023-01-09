<?php

namespace Sync\Laminas;

use Exception;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerExceptionInterface;

final class WorkerConnection
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
     * @return Pheanstalk
     */
    public static function instance(): Pheanstalk
    {
        try{
            if (self::$inst === null)
            {
                self::$config = (include "./config/autoload/beanstalk.global.php")['beanstalk']['worker'];
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
