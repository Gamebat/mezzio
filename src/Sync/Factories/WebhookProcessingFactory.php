<?php

declare(strict_types=1);

namespace Sync\Factories;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\WebhookProcessingHandler;

class WebhookProcessingFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new WebhookProcessingHandler();
    }
}
