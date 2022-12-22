<?php

namespace Sync\AmoAPI;

use Exception;

class CreateTokenFile
{
    /**
     * Сохраняем AccessToken в файл
     * @param array $tokenArray
     * @return void
     */
    public function saveToken(array $tokenArray): void
    {
        try {
            file_put_contents('./accessToken.json', json_encode([
                'access_token' => $tokenArray['accessToken'],
                'resource_owner_id' => $tokenArray['baseDomain'],
                'refresh_token' => $tokenArray['refreshToken'],
                'expires' => $tokenArray['expires'],
            ], JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            die("File 'accessToken.json' open Error");
        }
    }
}