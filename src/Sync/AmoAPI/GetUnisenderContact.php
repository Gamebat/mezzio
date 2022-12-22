<?php

namespace Sync\AmoAPI;

use Unisender\ApiWrapper\UnisenderApi;

class GetUnisenderContact
{
    /** @var UnisenderApi  */
    private UnisenderApi $client;

    /** @var string  */
    private string $token;

    public array $result;

    public function __construct()
    {
        $this->token = (include "./config/api.config.php")['uni_api_key'];
        $this->client = new UnisenderApi($this->token);
    }

    /**
     * ?
     * @param string $someData
     * @return string
     */
    public function getterContact(array $usersKommo): string
    {
        $id = 0;
        foreach ($usersKommo as $key => $name){
            foreach ($name as $key => $value)
                if ($key == 'emails'){
                    foreach ($value as $key => $value){
                        $this->result["data[{$id}][0]"] = $name['name'];
                        $this->result["data[{$id}][1]"] = $value;
                        $id++;
                    }

                }

        }
        return $this->client->importContacts(
            [
                'field_names[0]' => 'email',
                'field_names[1]' => 'name',
                $this->result
            ]
        );
        /*return $someData;*/
    }
}