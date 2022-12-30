<?php

namespace Sync\Producers;

use Pheanstalk\Pheanstalk;

class Producer
{
    public function produce($data)
    {
        $job = Pheanstalk::create('localhost', 11300)
            ->useTube('times')
            ->put(json_encode($data));
    }
}