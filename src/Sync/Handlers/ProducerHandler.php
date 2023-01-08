<?php
declare(strict_types=1);

namespace Sync\Handlers;

use Carbon\Carbon;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Producers\Producer;

class ProducerHandler implements RequestHandlerInterface
{

    public ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            (new Producer($this->container))
                ->produce("\nNow time: ". Carbon::now()->format('H:i (m.Y)'))
        );
    }
}
