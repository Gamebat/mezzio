<?php

namespace Sync\AmoAPI;

use Hopex\Simplog\Logger;
use Sync\Controllers\AccountController;
use Sync\models\Account;

class SaveUnisenderAPI
{
    /**
     * Сохранение api-ключа Unisender
     * в таблицу БД по имени пользователя
     * @param $postArray
     * @return Account|null
     */
    function saveApi(array $parsedBodyArray): ?Account
    {
        if (((new AccountController())->issetAccount($parsedBodyArray['Uname'])) === true)
        {
            return ((new AccountController())->saveUniToken($parsedBodyArray));
        } else {
            (new Logger())
                ->setLevel('requests')
                ->putData($parsedBodyArray, 'params');
            return null;
        }
    }
}