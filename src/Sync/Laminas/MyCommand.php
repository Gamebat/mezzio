<?php

namespace Sync\Laminas;

use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;

class MyCommand extends Command
{
    public function howTime(){
        printf("Now: %s", Carbon::now());
    }

}