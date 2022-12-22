<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;

class GetUsersEmails
{
    public array $result;
    public AmoCRMApiClient $apiClient;
    public function __construct(AmoCRMApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Получаем имена пользователей и их Email
     * @return array
     */
    public function getEmails(): array
    {
        try {
            $collection = $this->apiClient->contacts()->get();

            foreach ($collection as $id => $contact)
            {
                $this->result[$id]['name'] = $contact->getName();
                $field = $contact->getCustomFieldsValues()->getBy('field_code', 'EMAIL');

                if ($field != null) {
                    $email = $field->getValues();
                    foreach ($email as $value) {
                        $this->result[$id]['emails'][] = $value->getValue();
                    }
                } else {
                    $this->result[$id]['emails'][] = null;
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
