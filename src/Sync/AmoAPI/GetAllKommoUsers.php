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

    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Получаем имена пользователей и их Email
     * @return array
     */
    public function getUsers(): array
    {
        try
        {
            $collection = $this->apiClient->contacts()->get();
            foreach ($collection as $contact)
            {
                if ((($contact->getCustomFieldsValues()) !== null) && ($contact->getName()) !== null)
                {
                    $field = $contact->getCustomFieldsValues()->getBy('field_code', 'EMAIL');
                    $emails = $field->getValues();
                    if(($emails->isEmpty()) !== true)
                    {
                        $this->result[$this->id]['name'] = $contact->getName();
                        $this->result[$this->id]['id'] = $contact->getId();

                        foreach ($emails as $value)
                        {
                            $email = $value->toArray();

                            if ($email['enum_code'] === 'WORK')
                            {
                                $this->result[$this->id]['emails'][] = $value->getValue();
                            }

                        }
                        $this->id++;
                    }
                }

            }
        } catch (AmoCRMoAuthApiException|AmoCRMApiException $e){
            die($e->getMessage());
        }

        return $this->result;
    }
}
