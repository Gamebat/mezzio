<?php

namespace Sync\Workers;

use Sync\AmoAPI\RefreshTokens;

class RefreshWorker extends BaseWorker
{
    /** Вывод результата работы воркера
     * @param $data
     * @return void
     */
    protected string $queue = 'refresh';

    public function process($data): void
    {
        $result = (new RefreshTokens((int) $data))->refresh();

        echo "\n" . $result . " аккаунтов успешно обновлено";
    }
}
