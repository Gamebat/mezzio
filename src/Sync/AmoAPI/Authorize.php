<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\AccountModel;
use Exception;
use League\OAuth2\Client\Token\AccessToken;

class Authorize
{
    /**
     * Авторизуемся в Kommo
     * @return string
     */
    public function authorize(): string
    {
        session_start();
        $params = (include "./config/api.config.php");
        $apiClient = (new APIClient($params['clientId'], $params['clientSecret'], $params['redirectUri']
        ))->generateApiClient();

        try {

            if ((!file_exists('./accessToken.json')) || (empty(file_get_contents('./accessToken.json'))))
            {
                if (isset($_GET['referer'])) {
                    $apiClient->setAccountBaseDomain($_GET['referer']);
                }

                if (!isset($_GET['code'])) {
                    $state = bin2hex(random_bytes(16));
                    $_SESSION['oauth2state'] = $state;

                    if (isset($_GET['button'])) {
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
                    (new CreateTokenFile())->saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $apiClient->getAccountBaseDomain(),
                    ]);
                }
            } else {
                $json = file_get_contents("./accessToken.json");
                $array = json_decode($json, true);

                $apiClient
                    ->setAccessToken(new AccessToken($array))
                    ->setAccountBaseDomain((new AccessToken($array))->getResourceOwnerId());
            }

        } catch (AmoCRMApiException $e) {
            die((string)$e);
        } catch (Exception $e) {
            exit($e->getMessage());
        }

        return $apiClient
            ->account()
            ->getCurrent( AccountModel::getAvailableWith())
            ->getName();
    }
}
