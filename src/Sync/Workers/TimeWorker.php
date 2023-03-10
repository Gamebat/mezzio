<?php

namespace Sync\Workers;

class TimeWorker extends BaseWorker
{
    /** Вывод результата работы воркера
     * @param $data
     * @return void
     */
    protected string $queue = 'times';

    /** Вывод текущей даты
     * @param $data
     * @return void
     */
    public function process($data): void
    {
        echo $data;
    }
}
