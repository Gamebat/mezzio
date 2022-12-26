<?php

namespace Sync\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Sync\Models\Account;


class AccountController
{
    /**
     * @var Account
     */
    private Account $accountModel;
    public function __construct()
    {
        $this->accountModel = new Account();

        $capsule = new Capsule;

        $capsule->addConnection(include "./config/autoload/database.global.php");
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }

    /**
     * Получение имени аккаунта
     * @param string $name
     * @return Account|null
     */
    public function getAccountByName(string $name): ?Account
    {
        $account = $this->accountModel->where('name', $name)->get();
        return $account->isNotEmpty()
            ? $account->first()
            : $this->accountModel;
    }

    /**
     * Проверка: найдено ли имя из GET параметра в БД
     * @param string $name
     * @return bool
     */
    public function issetAccount(string $name): bool
    {
        return Account::where('name', $name)
            ->get()
            ->isNotEmpty();
    }

    /**
     * Сохранение имени и токена аккаунта в БД
     * @param array $tokenArray
     * @return Account
     */
    public function saveAuth(array $tokenArray): Account
    {
        return $this->accountModel->create($tokenArray);
    }
}
