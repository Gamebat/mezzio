<?php

namespace Sync\AmoAPI;

use Unisender\ApiWrapper\UnisenderApi;

class GetUnisenderContact
{
    /** @var UnisenderApi  */
    private UnisenderApi $client;

    /** @var string  */
    private string $token;

    public function __construct()
    {
        $this->token = (include "./config/unisender_config.php")['uni_api_key'];
        $this->client = new UnisenderApi($this->token);
    }

    /**
     * ?
     * @param string $someData
     * @return string
     */
    public function getterContact(string $someData): string
    {
        return $this->client->getContact(
            [
                'email' => $someData,
                'include_fields' => 1
            ]
        );
        /*return $someData;*/
    }
}