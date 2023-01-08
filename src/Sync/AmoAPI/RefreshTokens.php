<?php

namespace Sync\AmoAPI;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\OAuth2\Client\Provider\AmoCRMException;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sync\Controllers\AccountController;
use Sync\Models\Account;

class RefreshTokens
{
    public int $count = 0;
    public ?Account $account;

    public function __construct($hours)
    {
        $this->account = (new AccountController())->freshUser($hours);
    }

    public function refresh()
    {
        try {
            $params = (include "./config/api.config.php");
            $apiClient = (new AmoCRMApiClient(
                $params['clientId'],
                $params['clientSecret'],
                $params['redirectUri']));


            $tokenArray = $this->account->kommo_token;
            $accessToken = new AccessToken(json_decode($tokenArray, true));
            $apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain(($accessToken->getValues())['base_domain']);

            $newToken = $apiClient->getOAuthClient()->getAccessTokenByRefreshToken($accessToken);

            Account::chunk(50, function ($accounts) use ($newToken, $apiClient)
            {
                foreach ($accounts as $account)
                {
                    $this->count++;
                    (new AccountController())->saveAuth([
                            'name' => $account->name,
                            'kommo_token' => json_encode([
                                'access_token' => $newToken->getToken(),
                                'refresh_token' => $newToken->getRefreshToken(),
                                'expires' => $newToken->getExpires(),
                                'base_domain' => $apiClient->getAccountBaseDomain()
                            ])
                        ]
                    );
                }
            });
            return $this->count;
        } catch (Exception|AmoCRMException|AmoCRMMissedTokenException|AmoCRMoAuthApiException $e) {
            die($e->getMessage());
        }
    }
}