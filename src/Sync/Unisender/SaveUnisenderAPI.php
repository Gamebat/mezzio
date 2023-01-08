<?php

namespace Sync\Unisender;

use Sync\Controllers\AccountController;
use Sync\Models\Account;

class SaveUnisenderAPI
{
    /**
     * Сохранение api-ключа Unisender
     * в таблицу БД по имени пользователя
     * @param array $parsedBodyArray
     * @return Account|null
     */
    function saveApi(array $parsedBodyArray): ?Account
    {
        if (((new AccountController())->issetAccount($parsedBodyArray['Uname'])) === true)
        {
            return ((new AccountController())->saveUniToken($parsedBodyArray));
        } else {
            return null;
        }
    }
}