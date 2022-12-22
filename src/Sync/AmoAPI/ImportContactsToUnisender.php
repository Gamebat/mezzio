<?php

namespace Sync\AmoAPI;

use Unisender\ApiWrapper\UnisenderApi;

class ImportContactsToUnisender
{
    /** @var UnisenderApi  */
    private UnisenderApi $client;

    /** @var string  */
    private string $token;

    private array $result;
    public function __construct()
    {
        $this->token = (include "./config/unisender_config.php")['uni_api_key'];
        $this->client = new UnisenderApi($this->token);
    }
    /**
     * ?
     * @param array $usersKommo
     * @return string
     */
    public function getterContact(array $usersKommo): string
    {
        $id = 0;
        $this->result["field_names[0]"] = 'email';
        $this->result["field_names[1]"] = 'Name';
        foreach ($usersKommo as $key => $name)
        {
            foreach ($name as $key => $value)
                if ($key == 'emails'){
                    foreach ($value as $key => $value){
                        $this->result["data[{$id}][1]"] = $name['name'];
                        $this->result["data[{$id}][0]"] = $value;
                        $id++;
                    }

                }
        }

        return $this->client->importContacts($this->result);
    }
}
