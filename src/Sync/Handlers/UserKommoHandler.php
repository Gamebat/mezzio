<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\AmoAPI\APIClient;
use Sync\AmoAPI\AuthorizeKommo;
use Sync\AmoAPI\GetAllKommoUsers;

class UserKommoHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (include "./config/api.config.php");
        $apiClient = (new APIClient($params['clientId'], $params['clientSecret'], $params['redirectUri']))->generateApiClient();
        return new JsonResponse([(new GetAllKommoUsers($apiClient))->getUsers()]);
    }
}
