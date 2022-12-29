<?php

namespace Sync\Unisender;

use Exception;
use Hopex\Simplog\Logger;

class ContactAction
{
    /**
     * Добавление контакта
     * @param $parsedBodyArray
     * @return void
     */
    public function add($parsedBodyArray): void
    {
        try
        {
            foreach ($parsedBodyArray as $add)
            {
                if ((array_key_exists('custom_fields', $add)) === true) {
                    foreach ($add['custom_fields'] as $value) {
                        if ($value['code'] === 'EMAIL') {
                            foreach ($value['values'] as $email)
                            {
                                if ($email['enum'] == 89488)
                                {
                                    $this->result[] = [
                                        'contact_id' => $add['id'],
                                        'name' => $add['name'],
                                        'email' => $email['value']
                                    ];
                                } else {
                                    (new Logger())
                                        ->setLevel('errors')
                                        ->putData([$add['name'] => $email['value']], 'unsyncEmails');

                                }

                            }
                        }
                    }
                } else {
                    (new Logger())
                        ->setLevel('errors')
                        ->putData($parsedBodyArray, 'no_custom_fields');
                }
            }
            (new ImportOnAction('Alex', $this->result))->importContacts();
        } catch (Exception $e){
            (new Logger())
                ->setLevel('errors')
                ->putData($e->getMessage(), 'invalid_add');
        }

    }

    /**
     * Обновление аккаунта
     * @param array $parsedBodyArray
     * @return void
     */
    public function update(array $parsedBodyArray): void
    {
        $this->delete($parsedBodyArray);
        $this->add($parsedBodyArray);
    }

    /**
     * Удаление аккаунта
     * @param array $parsedBodyArray
     * @return void
     */
    public function delete(array $parsedBodyArray): void
    {
        foreach ($parsedBodyArray as $delete)
        {
            (new ImportOnAction('Alex', [$delete['id']]))->deleteContact();
        }
    }
}
