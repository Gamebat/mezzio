<?php

declare(strict_types=1);

namespace Sync\Factories;

use Sync\Laminas\MyCommand;

class MyCommandFactory
{
    public function __invoke()
    {
        return new MyCommand();
    }
}
