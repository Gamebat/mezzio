<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use Exception;
use Hopex\Simplog\Logger;
use Sync\Controllers\AccountController;

class Authorize
{
    /**
     * Авторизуемся в Kommo
     * @return string
     */
    public function authorize()
    {
        session_start();

        try
        {
            if (isset($_GET['name']))
            {
                $_SESSION['name'] = $_GET['name'];
            }

            $account = (new AccountController())->getAccountByName($_SESSION['name']);

            if ((new AccountController())->issetAccount($_SESSION['name']))
            {
                return (new AccountController())->getAccountByName($_SESSION['name'])->name;
            }

            $params = (include "./config/api.config.php");
            $apiClient = (new AmoCRMApiClient(
                $params['clientId'],
                $params['clientSecret'],
                $params['redirectUri']));

            if (isset($_GET['referer']))
            {
                $apiClient->setAccountBaseDomain($_GET['referer']);
            }

            if (!isset($_GET['code']))
            {
                $state = bin2hex(random_bytes(16));
                $_SESSION['oauth2state'] = $state;

                if (isset($_GET['button']))
                {
                    echo $apiClient
                        ->getOAuthClient()
                        ->setBaseDomain("www.kommo.com")
                        ->getOAuthButton(
                            [
                                'title' => 'Установить интеграцию',
                                'compact' => true,
                                'class_name' => 'className',
                                'color' => 'default',
                                'error_callback' => 'handleOauthError',
                                'state' => $state,
                            ]
                        );
                } else {
                    $authorizationUrl = $apiClient
                        ->getOAuthClient()
                        ->setBaseDomain("www.kommo.com")
                        ->getAuthorizeUrl([
                            'state' => $state,
                            'mode' => 'post_message',
                        ]);
                    header('Location: ' . $authorizationUrl);
                }
                die;
            } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
                exit('Invalid state');
            }

            /**
             * Ловим обратный код
             */
            $accessToken = $apiClient
                ->getOAuthClient()
                ->setBaseDomain($_GET['referer'])
                ->getAccessTokenByCode($_GET['code']);
            $apiClient
                ->setAccessToken($accessToken);

            if (!$accessToken->hasExpired()) {
                (new AccountController())->saveAuth([
                        'name' => $_SESSION['name'],
                        'kommo_token' => json_encode([
                            'access_token' => $accessToken->getToken(),
                            'refresh_token' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'base_domain' => $apiClient->getAccountBaseDomain()
                        ])
                    ]
                );
            }
            (new Logger())
                ->setLevel('subscribe')
                ->putData((new SubscribeWebhook())->subscribe($apiClient), 'response');

        } catch (AmoCRMApiException $e) {
            die((string)$e);
        } catch (Exception $e) {
            exit($e->getMessage());
        }

        return ((new AccountController())->getAccountByName($_SESSION['name'])->name);
    }
}
