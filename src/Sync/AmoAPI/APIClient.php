<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use Sync\Controllers\AccountController;

class APIClient
{

    /**
     * Генерация API клиента
     * @return AmoCRMApiClient
     */
    function generateApiClient($name): AmoCRMApiClient
    {
        $params = (include "./config/api.config.php");
        $apiClient = (new AmoCRMApiClient(
            $params['clientId'],
            $params['clientSecret'],
            $params['redirectUri']));

        $accessToken = json_decode((new AccountController())->takeKommoToken($name),true);
        $apiClient
            ->setAccessToken(new AccessToken($accessToken))
            ->setAccountBaseDomain($accessToken['base_domain']);
        return $apiClient;
    }
}
