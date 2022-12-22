<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Util\Exception;
use Unisender\ApiWrapper\UnisenderApi;
use Sync\AmoAPI\GetAllKommoUsers;
use Sync\AmoAPI\Authorize;

class ImportContactsToUnisender
{
    /** @var UnisenderApi  */
    private UnisenderApi $client;

    /** @var string  */
    private string $token;

    private array $usersKommo;

    private array $result;
    private array $contacts;
    public function __construct()
    {
        try {
            if ((!file_exists('./accessToken.json')) || (empty(file_get_contents('./accessToken.json'))))
            {
                throw new Exception('Ошибка авторизации');
            } else {
                $json = file_get_contents("./accessToken.json");
                $array = json_decode($json, true);
            }
            $clientId = "9c59de12-6982-4761-8967-c770ff9d544f";
            $clientSecret = "iwMJZLYZHrU7FUSbg0wHWSmkO3psJNGej7hVnwmGk2Djwh1DjDvV1s7tlgwdf4vB";
            $redirectUri = "https://0580-173-233-147-68.eu.ngrok.io/auth";
            $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
            $apiClient
                ->setAccessToken(new AccessToken($array))
                ->setAccountBaseDomain((new AccessToken($array))->getResourceOwnerId());
            $this->token = (include "./config/unisender_config.php")['uni_api_key'];
            $this->client = new UnisenderApi($this->token);
            $this->usersKommo = (new GetAllKommoUsers($apiClient))->getUsers();
        } catch (AmoCRMApiException|Exception $e){
            die($e->getMessage());
        }
    }
    /**
     * ?
     * @param array $usersKommo
     * @return string
     */
    public function importContacts(): array
    {
        $id = 0;
        foreach ($this->usersKommo as $key => $name)
        {
            foreach ($name as $key => $value)
                if ($key == 'emails'){
                    foreach ($value as $key => $value){
                        $this->contacts["data[{$id}][1]"] = $name['name'];
                        $this->contacts["data[{$id}][0]"] = $value;
                        $id++;
                    }

                }
        }

        if ((count($this->contacts)) > 2)
        {
            $number = intdiv((count($this->contacts)), 2);
            $arrays = array_chunk($this->contacts, $number, true);
            foreach ($arrays as $key => $value)
            {
                $value["field_names[0]"] = 'email';
                $value["field_names[1]"] = 'Name';
                $this->result[]=json_decode( $this->client->importContacts($value));
            }
        } else {
            $this->contacts["field_names[0]"] = 'email';
            $this->contacts["field_names[1]"] = 'Name';
            $this->result[]=json_decode($this->client->importContacts($this->contacts));
        }

        return $this->result;
    }
}
