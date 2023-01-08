<?php

namespace Sync\Controllers;

use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sync\Models\Account;


class AccountController
{
    /**
     * @var Account
     */
    private ?Account $accountModel;

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
    public function saveAuth(array $tokenArray): void
    {
        try {
            if ($this->issetAccount($tokenArray['name'])) {
                $this->accountModel = Account::where('name', $tokenArray['name'])->first();
                $this->accountModel->kommo_token = $tokenArray['kommo_token'];
                $this->accountModel->save();
            } else {
                $this->accountModel->create($tokenArray);
            }
        } catch (Exception|ModelNotFoundException $e) {
            die('saveAuth');
        }
    }

    /**
     * Сохранение api-ключа в таблицу БД
     * @param array $postArray
     * @return Account
     */
    public function saveUniToken(array $postArray): Account
    {
        $this->accountModel = Account::where('name', $postArray['Uname'])->first();
        $this->accountModel->unisender_api = $postArray['token'];
        return $this->accountModel->save();
    }

    /**
     * Получение Unisender токена
     * @param string $name
     * @return string
     */
    public function takeUniToken(string $name): string
    {
        $this->accountModel = Account::where('name', $name)->first();
        return $this->accountModel->unisender_api;
    }

    /**
     * Получение Kommo токена
     * @param string $name
     * @return array
     */
    public function takeKommoToken(string $name): string
    {
        try {
            $this->accountModel = Account::where('name', $name)->first();
            return $this->accountModel->kommo_token;
        } catch (\Exception $e) {
            die('Пользователь не найден');
        }
    }

    /**
     * Удаление Kommo токена у пользователя
     * @param string $name
     * @return void
     */
    public function deleteKommoToken(string $name): void
    {
        $this->accountModel = Account::where('name', $name)->first();
        $this->accountModel->kommo_token = null;
        $this->accountModel->save();
    }

    public function freshUser($hours): ?Account
    {
        try
        {
        /*Account::chunk(50, function ($accounts) use ($hours)
        {
            foreach ($accounts as $account)
            {
                $token = json_decode($account->kommo_token, true);

                $unixExpires = (new Carbon())->timestamp($token['expires']);
                if (((Carbon::now())->diffInHours($unixExpires)) <= $hours)
                {
                    return $account;
                }
            }
            return null;
        });*/


        foreach (Account::all() as $account)
        {
            $token = json_decode($account->kommo_token, true);
            $unixExpires = (new Carbon())->timestamp($token['expires']);
            if (((Carbon::now())->diffInHours($unixExpires)) < $hours)
            {
                return $account;
            }
        }

        return null;
        } catch (Exception $e) {
            die ($e->getMessage());
        }
    }
}
