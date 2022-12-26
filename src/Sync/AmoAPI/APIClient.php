<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;

class APIClient
{
    public string $clientId;
    public string $clientSecret;
    public string $redirectUri;
    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Генерация API клиента
     * @return AmoCRMApiClient
     */
    function generateApiClient()
    {
        $apiClient = new AmoCRMApiClient($this->clientId, $this->clientSecret, $this->redirectUri);
        if ((file_exists('./accessToken.json')) && (!empty(file_get_contents('./accessToken.json'))))
        {
            $json = file_get_contents("./accessToken.json");
            $array = json_decode($json, true);
            $apiClient
                ->setAccessToken(new AccessToken($array))
                ->setAccountBaseDomain($array['base_domain']);
        }

        return $apiClient;
    }
}
