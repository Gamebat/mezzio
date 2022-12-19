<?php

namespace Sync\AmoAPI;

use Exception;

class CreateTokenFile
{
    public function saveToken($array): void
    {
        try {
            file_put_contents('./accessToken.json', json_encode([
                'access_token' => $array['accessToken'],
                'resource_owner_id' => $array['baseDomain'],
                'refresh_token' => $array['refreshToken'],
                'expires_in' => $array['expires'],
                'expires' => $array['expires'],
            ], JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            die("File 'accessToken.json' open Error");
        }
    }
}