<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /*return new JsonResponse([
            'status' => 'ok'
        ]);*/
        $a = $request->getQueryParams()['first'];
        $b = $request->getQueryParams()['second'];
        $a1 = json_decode($a, true);
        $b1 = json_decode($b, true);

        $sum = $a1 + $b1;

        return new JsonResponse([$sum]);
    }
}