<?php

namespace Sync\AmoAPI;

use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\OAuth2\Client\Provider\AmoCRMException;
use Carbon\Carbon;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use Sync\Controllers\AccountController;
use Sync\Models\Account;

class RefreshTokens
{
    /**
     * @var int
     */
    public int $count = 0;

    /**
     * @var Account|null
     */
    public ?Account $account;

    public function __construct()
    {
        $this->account = (new AccountController())->freshUser();
    }

    /** Обновление токенов авторизации
     * @param $hours
     * @return int|null
     */
    public function refresh($hours): ?int
    {
        try {

            $apiClient = (new APIClient())->generateApiClient($this->account->name);
            $accessToken = (new AccessToken(json_decode($this->account->kommo_token,true)));

            $newToken = $apiClient
                ->getOAuthClient()
                ->getAccessTokenByRefreshToken($accessToken);

            Account::chunk(50, function ($accounts) use ($newToken, $apiClient, $hours)
            {
                foreach ($accounts as $account)
                {
                    $token = json_decode($account->kommo_token, true);
                    $unixExpires = (new Carbon())->timestamp($token['expires']);
                    if (((Carbon::now())->diffInHours($unixExpires)) <= $hours)
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
                }
            });

            return $this->count;
        } catch (Exception|AmoCRMException|AmoCRMMissedTokenException|AmoCRMoAuthApiException $e) {
            die($e->getMessage());
        }
    }
}
