<?php
declare(strict_types=1);

namespace Sync\Handlers;

use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\AccountModel;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Client\Token\AccessTokenInterface;

use AmoCRM\Client\AmoCRMApiClient;

session_start();

class TestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([$this->takeCode()]);
        /*$a = $request->getQueryParams()['first'];
        $b = $request->getQueryParams()['second'];

        $sum = $a + $b;

        return new JsonResponse([$sum]);*/
    }


    public function takeCode()
    {
        $clientId = "9c59de12-6982-4761-8967-c770ff9d544f";
        $clientSecret = "iwMJZLYZHrU7FUSbg0wHWSmkO3psJNGej7hVnwmGk2Djwh1DjDvV1s7tlgwdf4vB";
        $redirectUri = "https://ee08-173-233-147-68.eu.ngrok.io/test";

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        if (trim(file_get_contents('accessToken.json')) == false) {
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
                    die;
                } else {
                    $authorizationUrl = $apiClient
                        ->getOAuthClient()
                        ->setBaseDomain("www.kommo.com")
                        ->getAuthorizeUrl([
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
            try {
                $accessToken = $apiClient
                    ->getOAuthClient()
                    ->setBaseDomain($_GET['referer'])
                    ->getAccessTokenByCode($_GET['code']);

                if (!$accessToken->hasExpired()) {
                    $this->saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $apiClient->getAccountBaseDomain(),
                    ]);
                }
                $apiClient
                    ->setAccessToken($this->takeToken($accessToken))
                    ->setAccountBaseDomain($apiClient->getAccountBaseDomain());
                $account = $apiClient->account()->getCurrent( AccountModel::getAvailableWith());

            } catch (Exception $e) {
                die((string)$e);
            }
        } else{
            $json = file_get_contents("accessToken.json");
            $array = json_decode($json, true);
            $accToken = new AccessToken($array);

            $token = $this->takeToken($accToken);

            $apiClient
                ->setAccessToken($token)
                ->setAccountBaseDomain($token->getResourceOwnerId());

            /*$field = $apiClient->contacts()->get()->pluck('name');
            foreach ($field as $id => $item)
            {
                $contacts[$id]['name'] = $item;
            }*/

            $field2 = $apiClient->contacts()->get()->pluck('custom_fields_values');
            $collection = $apiClient->contacts()->get();

            $result = [];

            foreach ($collection as $id => $contact) {
                $result[$id]['name'] = $contact->getName();
            }

            foreach ($collection as $id => $contact){
                $field = $contact -> getCustomFieldsValues() -> getBy('field_code','EMAIL');

                /*if ($field != null){

                    $result[$id]['field'] = $field -> getValues();
                }*/
                if ($field != null)
                {

                    $email = $field -> getValues();
                    foreach ($email as $key => $value)
                    {
                        foreach ($key[$value] as $ss => $item)
                        {
                            $result[]['test'] = $item;
                            if ($key === 'value' || 1 == 1){
                                $result[$id]['email'] = $item;
                            }
                        }
                    }

                }

            }


            /*foreach ($field2 as $id => $item)
            {
                $contacts[$id]['fields'] = $item;
            }*/

            //На крайний случай

            /*$field = $apiClient->contacts()->get()->pluck('name');
            foreach ($field as $id => $item)
            {
                $result[$id]['name'] = $item;
            }
            $field2 = $apiClient->contacts()->get()->pluck('custom_fields_values');
            $find = 0;
            foreach ($field2 as $id => $item)
            {
                //$contacts[$id]['first'] = $item;
                foreach ($item[0] as $key => $value)
                {
                    if ($value === 'Email')
                    {
                        $find = 1;
                    }
                }
                if ($find === 1)
                {
                    foreach ($item[0]['values'] as $key => $value)
                    {
                        foreach ($value as $key => $value)
                        {
                            if ($key === 'value')
                            {
                                $result[$id]['email'] = $value;
                            }

                        }
                    }
                }
            }*/
            return $result;
        }
        return $contacts;
    }

    public function saveToken($array): void
    {
        file_put_contents('accessToken.json', json_encode($array, JSON_PRETTY_PRINT));

        $data['access_token'] = $array['accessToken'];
        $data['resource_owner_id'] = $array['baseDomain'];
        $data['refresh_token'] = $array['refreshToken'];
        $data['expires_in'] = $array['expires'];
        $data['expires'] = $array['expires'];


        file_put_contents('accessToken.json', json_encode($data, JSON_PRETTY_PRINT));

    }

    public function takeToken(AccessTokenInterface $accessToken)
    {
        $accessToken->getToken();
        return $accessToken;
    }


}