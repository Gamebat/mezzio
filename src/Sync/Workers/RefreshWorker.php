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

    /** Выполнение работы воркера по обновлению
     * токенов авторизаци
     * @param $data
     * @return void
     */
    public function process($data): void
    {
        $result = (new RefreshTokens())->refresh((int) $data);
        echo "\n" . $result . " - аккаунтов успешно обновлено";
    }
}
