<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Carbon\Carbon;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Controllers\AccountController;
use Sync\Producers\Producer;

class TestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse((new AccountController())->freshUser(24));
    }
}
