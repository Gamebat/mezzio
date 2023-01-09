<?php

namespace Sync\AmoAPI;

use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\OAuth2\Client\Provider\AmoCRMException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use League\OAuth2\Client\Token\AccessToken;
use Sync\Controllers\AccountController;
use Sync\Models\Account;

class RefreshTokens
{
    /**
     * @var Account|null
     */
    public ?Account $account;

    public function __construct()
    {
        $this->account = (new AccountController())->freshUser();
    }

    /** Обновление токенов авторизации
     * @param int $hours
     * @return int|null
     */
    public function refresh(int $hours): ?int
    {
        try {

            $apiClient = (new APIClient())->generateApiClient($this->account->name);
            $accessToken = (new AccessToken(json_decode($this->account->kommo_token,true)));

            $newToken = $apiClient
                ->getOAuthClient()
                ->getAccessTokenByRefreshToken($accessToken);

            /** @var Collection $accounts*/
            $accounts = (new Account())->hasExpired($hours);
            foreach ($accounts as $account)
            {
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
            return $accounts->count();

        } catch (Exception|AmoCRMException|AmoCRMMissedTokenException|AmoCRMoAuthApiException $e) {
            die($e->getMessage());
        }
    }
}
