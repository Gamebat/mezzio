<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\AmoAPI\Authorize;
use Sync\AmoAPI\GetUnisenderAPI;

class GetUnisederAPIHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse((new GetUnisenderAPI())->saveUnisenderApi($request->getParsedBody()));
    }
}
