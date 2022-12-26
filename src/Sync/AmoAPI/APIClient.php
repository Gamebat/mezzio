<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;

class APIClient
{
    /**
     * @var string
     */
    public string $clientId;

    /**
     * @var string
     */
    public string $clientSecret;

    /**
     * @var string
     */
    public string $redirectUri;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
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
