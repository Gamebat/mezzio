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
    function generateApiClient(): AmoCRMApiClient
    {
        $apiClient = new AmoCRMApiClient($this->clientId, $this->clientSecret, $this->redirectUri);
        return $apiClient;
    }
}
