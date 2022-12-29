<?php

namespace Sync\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Sync\Models\Contact;


class ContactController
{
    /**
     * @var Contact
     */
    private Contact $contactModel;

    public function __construct()
    {
        $this->contactModel = new Contact();

        $capsule = new Capsule;

        $capsule->addConnection(include "./config/autoload/database.global.php");
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }

    /**
     * Сохранение контакта в таблицу БД
     * @param array $contactArray
     * @return Contact
     */
    public function saveContact(array $contactArray): Contact
    {
        return $this->contactModel->create($contactArray);
    }

    /**
     * Удаление контакта из таблицы БД
     * @param array $emails
     * @return void
     */
    public function deleteContactDB(array $emails): array
    {
        foreach ($emails as $email)
        {
           return Contact::where('email', $email)->delete();
        }
    }

    /**
     * Получение всех Email контакта
     * @param int $id
     * @return array
     */
    public function getContact(int $id): array
    {
        $this->accountModel = Contact::where('contact_id', (int)$id)->get()->toArray();

        foreach ($this->accountModel as $account)
        {
            $result[]= $account['email'];
        }

        return $result;
    }
}
