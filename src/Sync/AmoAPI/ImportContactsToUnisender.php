<?php

namespace Sync\AmoAPI;

use AmoCRM\Exceptions\AmoCRMApiException;
use PHPUnit\Util\Exception;
use Unisender\ApiWrapper\UnisenderApi;

class ImportContactsToUnisender
{
    /**
     * @var UnisenderApi
     */
    private UnisenderApi $client;

    /**
     * @var string
     */
    private string $token;

    /**
     * @var array
     */
    private array $usersKommo;

    /**
     * @var array
     */
    private array $contacts;

    /**
     * @var array
     */
    private array $blocks;

    public function __construct()
    {
        try
        {
            $params = (include "./config/api.config.php");
            $apiClient = (new APIClient($params['clientId'], $params['clientSecret'], $params['redirectUri']))->generateApiClient();

            $this->token = $params['uni_api_key'];
            $this->client = new UnisenderApi($this->token);
            $this->usersKommo = (new GetAllKommoUsers($apiClient))->getUsers();
        } catch (AmoCRMApiException|Exception $e){
            die($e->getMessage());
        }
    }

    /**
     * Синхронизация контактов Kommo с Unisender
     * @return array
     */
    public function importContacts(): array
    {
        $header = [
            'field_names[0]' => 'email',
            'field_names[1]' => 'Name'
        ];
        $id = 0;
        foreach ($this->usersKommo as $name)
        {
            foreach ($name as $key => $value)
            {
                if ($key == 'emails') {
                    foreach ($value as $email) {
                        $this->contacts["data[{$id}][1]"] = $name['name'];
                        $this->contacts["data[{$id}][0]"] = $email;
                        $id++;
                    }

                }
            }
        }
        $result = [];
        $this->blocks = array_chunk($this->contacts, 500, true);

        foreach ($this->blocks as $block){
            $block  = array_merge($block, $header);
            $result[]=json_decode($this->client->importContacts($block));
        }

        return $result;
    }
}
