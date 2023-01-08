<?php

namespace Sync\Unisender;

use PHPUnit\Util\Exception;
use Sync\AmoAPI\APIClient;
use Sync\AmoAPI\GetAllKommoUsers;
use Sync\Controllers\AccountController;
use Sync\Controllers\ContactController;
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

    private array $toDatabase;

    public function __construct($name)
    {
        try
        {
            $apiClient = (new APIClient())->generateApiClient($name);

            $this->token = (new AccountController())->takeUniToken($name);
            $this->client = new UnisenderApi($this->token);
            $this->usersKommo = (new GetAllKommoUsers($apiClient))->getUsers();
        } catch (Exception $e){
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
                if ($key == 'emails')
                {
                    foreach ($value as $email)
                    {
                        $this->toDatabase[$email] = [
                            'name' => $name['name'],
                            'contact_id' => $name['id']
                        ];
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
            $inserted = json_decode($this->client->importContacts($block),true);
            if ((count($inserted['result']['log']))!=0)
            {
                foreach ($inserted['result']['log'] as $value)
                {
                    $index = $value['index'];
                    $unseted = $block["data[{$index}][0]"];
                    $unsetedName = $block["data[{$index}][1]"];

                    unset($this->toDatabase[$unseted]);
                }
            }

            foreach ($this->toDatabase as $key => $value){
                (new ContactController())->saveContact([
                    'contact_id' => $value['contact_id'],
                    'name' => $value['name'],
                    'email' => $key
                ]);
            }

            $result[]= $inserted;
        }

        return $result;
    }
}
