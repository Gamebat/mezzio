<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use Hopex\Simplog\Logger;
use League\OAuth2\Client\Token\AccessToken;
use Sync\Controllers\AccountController;

class APIClient
{
    /**
     * Генерация API клиента
     * @param $name
     * @return AmoCRMApiClient
     */
    function generateApiClient($name): AmoCRMApiClient
    {
        try
        {
            $params = (include "./config/api.config.php");
            $apiClient = (new AmoCRMApiClient(
                $params['clientId'],
                $params['clientSecret'],
                $params['redirectUri']));

            $accessToken = json_decode((new AccountController())->takeKommoToken($name),true);

            if ($accessToken){
                $apiClient
                    ->setAccessToken(new AccessToken($accessToken))
                    ->setAccountBaseDomain($accessToken['base_domain']);
            } else {
                throw new \Exception('Нет токена авторизации');
            }
            $check = $apiClient->contacts()->get();
            return $apiClient;
        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|\Exception $e){
            (new Logger())
                ->setLevel('errors')
                ->putData($e->getMessage(), 'generate_client');

            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' .
                $_SERVER['HTTP_HOST'] .
                '/auth?name=' .
                $_REQUEST['name'];

            (new AccountController())->deleteKommoToken($name);

            header('Location: '.$url);
            exit;
        }
    }
}
