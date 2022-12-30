<?php

namespace Sync\Workers;

class TimeWorker extends BaseWorker
{
    public function process($data): void
    {
        echo $data;
    }
}
