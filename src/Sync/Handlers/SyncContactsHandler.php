<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Unisender\ImportContactsToUnisender;

class SyncContactsHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($request->getQueryParams()['name'])){
            return new JsonResponse ("Введите имя в GET параметры");
        }
        return new JsonResponse((new ImportContactsToUnisender($request->getQueryParams()['name']))->importContacts());
    }
}
