<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;

class GetAllKommoUsers
{
    public array $result;
    public int $idd = 0;
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

            foreach ($collection as $id => $contact)
            {
                $field = $contact->getCustomFieldsValues()->getBy('field_code', 'EMAIL');

                if ($field != null) {
                    $this->result[$this->idd]['name'] = $contact->getName();
                    $email = $field->getValues();
                    foreach ($email as $value) {
                        $this->result[$this->idd]['emails'][] = $value->getValue();
                    }
                    $this->idd++;
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
