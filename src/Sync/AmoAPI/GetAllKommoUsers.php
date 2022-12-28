<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use function PHPUnit\Framework\isEmpty;

class GetAllKommoUsers
{
    /**
     * @var array
     */
    public array $result;
    public array $asd;

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
    public function getUsers()
    {
        try {
            $collection = $this->apiClient->contacts()->get();

            foreach ($collection as $contact)
            {

                if ((($contact->getCustomFieldsValues()) !== null) && ($contact->getName()) !== null)
                {
                    $field = $contact->getCustomFieldsValues()->getBy('field_code', 'EMAIL');


                    $email = $field->getValues();
                    if(($email->isEmpty()) !== true)
                    {
                        $this->result[$this->id]['name'] = $contact->getName();
                        $this->result[$this->id]['id'] = $contact->getId();

                        foreach ($email as $value)
                        {
                            if (($value->getValue()) !== ''){
                                $this->result[$this->id]['emails'][] = $value->getValue();
                            }

                        }
                        $this->id++;
                    }
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
