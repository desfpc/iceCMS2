<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * JWT class
 */

namespace iceCMS2\Authorization;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class JWT
{
    /** @var string[] Allowed token types */
    private const ALLOWED_TYPES = ['refresh', 'access'];

    /** @var string[] Access token lifetime */
    private const ACCESS_TOKEN_LIFETIME = 3600;

    /** @var string[] Refresh token lifetime */
    private const REFRESH_TOKEN_LIFETIME = 2592000;

    /** @var Settings Token algorithm */
    private const ALGORITHM = 'sha256';

    /**
     * Get JWT token
     *
     * @param Settings $settings
     * @param int $userID
     * @param string $type
     * @return string
     * @throws Exception
     */
    public static function getJWT(Settings $settings, int $userID, string $type): string
    {
        if (!in_array($type,self::ALLOWED_TYPES)) {
            throw new Exception('Wrong token type');
        }

        $header = json_encode(['typ' => 'JWT', 'alg' => 'SHA256']);

        $payload = [];
        switch ($type) {
            case 'refresh':
                $payload = [
                    'iss' => 'iceCMS2',
                    'iat' => time(),
                    'exp' => time() + self::REFRESH_TOKEN_LIFETIME,
                    'uid' => $userID,
                    'type' => 'refresh'
                ];
                break;
            case 'access':
                $payload = [
                    'iss' => 'iceCMS2',
                    'iat' => time(),
                    'exp' => time() + self::ACCESS_TOKEN_LIFETIME,
                    'uid' => $userID,
                    'type' => 'access'
                ];
                break;
        }

        $payload = json_encode($payload);
        $signature = hash_hmac(self::ALGORITHM, $header . '.' . $payload, $settings->secret);

        return base64_encode($header) . '.' . base64_encode($payload) . '.' . base64_encode($signature);
    }

    /**
     * Check JWT token
     *
     * @param Settings $settings
     * @param string $token
     * @return bool|JWTPayload
     */
    public static function checkJWT(Settings $settings, string $token): bool|JWTPayload
    {
        $tokenArr = explode('.', $token);
        $header = json_decode(base64_decode($tokenArr[0]), true);
        $payload = json_decode(base64_decode($tokenArr[1]), true);
        $signature = base64_decode($tokenArr[2]);

        if ($signature === hash_hmac(self::ALGORITHM, $header . '.' . $payload, $settings->secret)) {
            if ($payload['exp'] > time()) {
                return self::getPayloadObj($payload);
            }
        }

        return false;
    }

    /**
     * Get JWT token payload
     *
     * @param array $payload
     * @return JWTPayload
     */
    public static function getPayloadObj(array $payload): JWTPayload
    {
        return new JWTPayload($payload['iss'], $payload['iat'], $payload['exp'], $payload['uid'], $payload['type']);
    }
}