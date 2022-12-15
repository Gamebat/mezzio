<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use AmoCRM\Client\AmoCRMApiClient;

session_start();

class TestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->takeCode());
        /*$a = $request->getQueryParams()['first'];
        $b = $request->getQueryParams()['second'];

        $sum = $a + $b;

        return new JsonResponse([$sum]);*/
    }

    public function takeCode(): void{


        $clientId = "9c59de12-6982-4761-8967-c770ff9d544f";
        $clientSecret = "iwMJZLYZHrU7FUSbg0wHWSmkO3psJNGej7hVnwmGk2Djwh1DjDvV1s7tlgwdf4vB";
        $redirectUri = "https://webhook.site/a13a57db-6fa8-4ca4-87ef-c62a8d35c1a8";

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        if (isset($_GET['referer'])) {
            $apiClient->setAccountBaseDomain($_GET['referer']);
        }


        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;
            if (isset($_GET['button'])) {
                echo $apiClient->getOAuthClient()->setBaseDomain("www.kommo.com")->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );
                die;
            } else {
                $authorizationUrl = $apiClient->getOAuthClient()->setBaseDomain("www.kommo.com")->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
                die;
            }
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }
        

        /**
         * Ловим обратный код
         */
        /*try {
            $accessToken = $apiClient->getOAuthClient()->setBaseDomain("www.kommo.com")->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $apiClient->getAccountBaseDomain(),
                ]);
            }
        } catch (Exception $e) {
            die((string)$e);
        }

        $ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

        printf('Hello, %s!', $ownerDetails->getName());*/
    }


}