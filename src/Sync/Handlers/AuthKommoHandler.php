<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\AmoAPI\Authorize;

class AuthKommoHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!empty($request->getQueryParams()['name']) || !empty($request->getQueryParams()['code'])) {
            return new JsonResponse((new Authorize())->authorize());
        } else {
            return new JsonResponse('Ошибка! Введите GET параметры!');
        }
    }
}
