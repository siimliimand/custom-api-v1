<?php

namespace App\Google;

use App\Exception\InvalidGoogleIdTokenException;
use DateTime;
use DateTimeZone;
use Exception;

class IdTokenVerifier
{

    public const OPENID_CONFIGURATION_URL = 'https://accounts.google.com/.well-known/openid-configuration';

    protected static $payload;

    /**
     * @param string $idToken
     * @return bool
     * @throws InvalidGoogleIdTokenException
     * @throws Exception
     */
    public static function verify(string $idToken): bool
    {
        $decodedJWT = static::decodeJWT($idToken);

        try {
            $keys = static::getKeys();
            $ok = $keys !== null &&
                static::verifySignature($decodedJWT, $keys) &&
                static::verifyPayload($decodedJWT['payload'], appGet('google.client_id'));
            if ($ok) {
                static::$payload = $decodedJWT['payload'];
            }
            return $ok;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getPayload(): ?array
    {
        return static::$payload;
    }

    /**
     * @param string $idToken
     * @return array
     * @throws InvalidGoogleIdTokenException
     */
    protected static function decodeJWT(string $idToken): array
    {
        $token = explode('.', $idToken);
        if (count($token) !== 3) {
            throw new InvalidGoogleIdTokenException(
                translate('messages.error.invalid_google_id_token')
            );
        }

        $headerSegment = json_decode(base64_decode($token[0]), true);
        $payloadSegment = json_decode(base64_decode($token[1]), true);
        $signature = preg_replace(
            '/_/',
            '/',
            preg_replace(
            '/\-/',
            '+',
            $token[2] . str_repeat('=', (5 - strlen($token[2]) % 4))
            )
        );

        return [
            'dataToSign' => implode('.', [$token[0], $token[1]]),
            'header' => $headerSegment,
            'payload' => $payloadSegment,
            'signature' => $signature
        ];
    }

    /**
     * @param array $jwt
     * @param array $keys
     * @return bool
     */
    protected static function verifySignature(array $jwt, array $keys): bool
    {
        $kid = $jwt['header']['kid'] ?? null;
        if ($kid === null) {
            return false;
        }

        $key = static::findKey($keys, $kid);
        $pem = static::createPemFromModulusAndExponent($key['n'], $key['e']);
        $pubKeyId = openssl_pkey_get_public($pem);
        $data = $jwt['dataToSign'];
        $signature = base64_decode($jwt['signature']);

        return openssl_verify($data, $signature, $pubKeyId,OPENSSL_ALGO_SHA256);
    }

    /**
     * @param array $payload
     * @param string $googleClientId
     * @return bool
     * @throws Exception
     */
    protected static function verifyPayload(array $payload, string $googleClientId): bool
    {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('UTC'));

        if (isset($payload['iss'], $payload['exp'], $payload['aud']) === false) {
            return false;
        }

        try {
            return in_array($payload['iss'], ['accounts.google.com', 'https://accounts.google.com']) &&
                $payload['aud'] === $googleClientId &&
                $now->getTimestamp() < $payload['exp'];
        } catch (Exception $e) {
            return false;
        }
    }

    protected static function getKeys(): ?array
    {
        $openIdConfigurationJson = file_get_contents(static::OPENID_CONFIGURATION_URL);
        $openIdConfigurationData = json_decode($openIdConfigurationJson, false);
        $jwksUri = $openIdConfigurationData->jwks_uri ?? null;
        if ($jwksUri === null) {
            return null;
        }
        $jwksJson = file_get_contents($jwksUri);
        $jwksData = json_decode($jwksJson, true);

        return $jwksData['keys'] ?? null;
    }

    /**
     * @param array $keys
     * @param string $kid
     * @return array|null
     */
    protected static function findKey(array $keys, string $kid): ?array
    {
        foreach ($keys as $key) {
            if ($key['kid'] === $kid) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param $n
     * @param $e
     * @return string
     */
    protected static function createPemFromModulusAndExponent($n, $e): string
    {
        $modulus = static::urlsafeB64Decode($n);
        $publicExponent = static::urlsafeB64Decode($e);

        $components = array(
            'modulus' => pack('Ca*a*', 2, self::encodeLength(strlen($modulus)), $modulus),
            'publicExponent' => pack('Ca*a*', 2, self::encodeLength(strlen($publicExponent)), $publicExponent)
        );

        $RSAPublicKey = pack(
            'Ca*a*a*',
            48,
            self::encodeLength(strlen($components['modulus']) + strlen($components['publicExponent'])),
            $components['modulus'],
            $components['publicExponent']
        );

        $rsaOID = pack('H*', '300d06092a864886f70d0101010500');
        $RSAPublicKey = chr(0) . $RSAPublicKey;
        $RSAPublicKey = chr(3) . self::encodeLength(strlen($RSAPublicKey)) . $RSAPublicKey;

        $RSAPublicKey = pack(
            'Ca*a*',
            48,
            self::encodeLength(strlen($rsaOID . $RSAPublicKey)),
            $rsaOID . $RSAPublicKey
        );

        $RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
            chunk_split(base64_encode($RSAPublicKey), 64) .
            '-----END PUBLIC KEY-----';

        return $RSAPublicKey;
    }

    /**
     * @param $input
     * @return bool|string
     */
    protected static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param $length
     * @return false|string
     */
    protected static function encodeLength($length)
    {
        if ($length <= 0x7F) {
            return chr($length);
        }
        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }
}