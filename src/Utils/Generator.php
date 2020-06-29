<?php

namespace App\Utils;

use \Firebase\JWT\JWT;

class Generator
{
    public function accessToken($username = '')
    {
        $key = $_ENV['JWT_PASSPHRASE'];
        $payload = array(
            'iss' => $_ENV['URI'],
            'aud' => $_ENV['URI'],
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'username' => $username,
        );

        return JWT::encode($payload, $key);
    }
}
