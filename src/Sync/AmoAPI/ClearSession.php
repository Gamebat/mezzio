<?php

namespace Sync\AmoAPI;

class ClearSession
{
    function clear(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        return "session unseated";
    }
}