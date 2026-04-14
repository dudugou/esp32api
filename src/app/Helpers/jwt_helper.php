<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

if (!function_exists('getJWTSecretKey')) {
    /**
     * JWT秘密鍵を取得
     */
    function getJWTSecretKey(): string
    {
        return getenv('JWT_SECRET_KEY') ?: 'your-secret-key-change-this-in-production';
    }
}

if (!function_exists('generateJWT')) {
    /**
     * JWTトークンを生成
     *
     * @param array $userData ユーザーデータ
     * @param int $expireTime 有効期限（秒）デフォルトは24時間
     * @return string
     */
    function generateJWT(array $userData, int $expireTime = 86400): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $expireTime;

        $payload = [
            'iat' => $issuedAt,           // 発行時刻
            'exp' => $expire,             // 有効期限
            'iss' => base_url(),          // 発行者
            'data' => [
                'id'     => $userData['id'],
                'userid' => $userData['userid'],
                'email'  => $userData['email'] ?? null,
            ]
        ];

        return JWT::encode($payload, getJWTSecretKey(), 'HS256');
    }
}

if (!function_exists('verifyJWT')) {
    /**
     * JWTトークンを検証
     *
     * @param string $token
     * @return object|false
     */
    function verifyJWT(string $token)
    {
        try {
            $decoded = JWT::decode($token, new Key(getJWTSecretKey(), 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            log_message('error', 'JWT verification failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getJWTFromRequest')) {
    /**
     * リクエストからJWTトークンを取得
     *
     * @return string|null
     */
    function getJWTFromRequest(): ?string
    {
        $request = Services::request();
        $header = $request->getHeaderLine('Authorization');

        if (!empty($header)) {
            if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
