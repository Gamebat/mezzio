<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;

class GetAllKommoUsers
{
    /**
     * @var array
     */
    public array $result;

    /**
     * @var int
     */
    public int $id = 0;

    /**
     * @var AmoCRMApiClient
     */
    public AmoCRMApiClient $apiClient;

    public function __construct(AmoCRMApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Получаем имена пользователей и их Email
     * @return array
     */
    public function getUsers(): array
    {
        try {
            $collection = $this->apiClient->contacts()->get();

            foreach ($collection as $contact)
            {
                $field = $contact->getCustomFieldsValues()->getBy('field_code', 'EMAIL');

                if ($field != null)
                {
                    $this->result[$this->id]['name'] = $contact->getName();
                    $email = $field->getValues();
                    foreach ($email as $value)
                    {
                        $this->result[$this->id]['emails'][] = $value->getValue();
                    }
                    $this->id++;
                }

            }
        } catch (AmoCRMoAuthApiException $e){
            die('Ошибка авторизации');
        } catch (AmoCRMApiException $e){
            die($e->getMessage());
        }

        return $this->result;
    }
}
