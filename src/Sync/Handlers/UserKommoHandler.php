<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\AmoAPI\AuthorizeKommo;
use Sync\AmoAPI\GetAllKommoUsers;

class UserKommoHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($_GET['name'])){
            return new JsonResponse ("Введите имя в GET параметры");
        }
        return new JsonResponse([(new GetAllKommoUsers($_GET['name']))->getUsers()]);
    }
}
