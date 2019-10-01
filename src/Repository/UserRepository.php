<?php

namespace App\Repository;

use App\DB\DB;

class UserRepository
{
    public const TABLE_NAME = 'users';

    public static $apiTokenUserIds = [];

    /**
     * @param string $googleId
     * @return array|null
     */
    public static function getUserByGoogleId(string $googleId): ?array
    {
        $user = static::_getUserByGoogleId($googleId);
        if($user === null || ($apiToken = static::updateApiToken($user['id'])) === false) {
            return null;
        }
        $user['api_token'] = $apiToken;

        return $user;
    }

    /**
     * @param string $apiToken
     * @return int|null
     */
    public static function getUserIdByApiToken(string $apiToken): ?int
    {
        if (array_key_exists($apiToken, static::$apiTokenUserIds)) {
            return static::$apiTokenUserIds[$apiToken];
        }

        $tableName = static::TABLE_NAME;
        $sql = "
        SELECT `id`
          FROM `$tableName`
         WHERE `api_token` = :api_token
        ";
        $params = [
            'api_token' => $apiToken
        ];
        $stmt = DB::execute($sql, $params);
        if ($stmt) {
            $data = $stmt->fetch();
            if (isset($data['id'])) {
                static::$apiTokenUserIds[$apiToken] = $data['id'];
                return $data['id'];
            }
        }

        return null;
    }

    /**
     * @param string $googleId
     * @return array|null
     */
    protected static function _getUserByGoogleId(string $googleId): ?array
    {
        $usersTableName = static::TABLE_NAME;
        $socialAccountsTableName = SocialAccountsRepository::TABLE_NAME;
        $statusTableName = StatusRepository::TABLE_NAME;

        $sql = "
        SELECT u.*
          FROM `$usersTableName` AS u
          JOIN `$socialAccountsTableName` AS sa ON sa.`user_id` = u.id
          JOIN `$statusTableName` AS us ON u.`status_id` = us.id
         WHERE sa.`provider_user_id` = :provider_user_id
           AND sa.`provider` = :provider 
           AND us.`code` <> :status
        ";
        $properties = [
            'provider_user_id' => $googleId,
            'provider' => SocialAccountsRepository::PROVIDER_GOOGLE,
            'status' => StatusRepository::STATUS_DELETED
        ];

        $stmt = DB::execute($sql, $properties);
        if ($user = $stmt->fetch()) {
            return $user;
        }

        return null;
    }

    /**
     * @param int $userId
     * @return string|null
     */
    public static function updateApiToken(int $userId): ?string
    {
        $tableName = static::TABLE_NAME;
        $sql = "
        UPDATE `$tableName` 
           SET `api_token` = :api_token
         WHERE `id` = :id
        ";
        $apiToken = static::generateApiToken($userId);
        $params = [
            'api_token' => $apiToken,
            'id' => $userId
        ];

        return DB::execute($sql, $params) !== null ? $apiToken : null;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $apiToken
     * @param string $statusCode
     * @param string $socialAccountProvider
     * @param string $socialAccountProviderUserId
     * @return bool
     */
    public static function createNewUser(
        string $name,
        string $email,
        string $apiToken,
        string $statusCode,
        string $socialAccountProvider,
        string $socialAccountProviderUserId
    ): bool {
        $tableName = static::TABLE_NAME;
        $sql = "
            INSERT INTO $tableName
            (`name`, `email`, `api_token`, `status_id`, `created_at`, `updated_at`)
            VALUES
            (:name, :email, :api_token, :status_id, NOW(), NOW())
        ";
        $params = [
            'name' => $name,
            'email' => $email,
            'api_token' => $apiToken,
            'status_id' => StatusRepository::getStatusIdByCode($statusCode)
        ];
        $stmt = DB::execute($sql, $params);

        if ($stmt !== null) {
            $userId = DB::getLastInsertId();

            return SocialAccountsRepository::createNewSocialAccount(
                $userId,
                $socialAccountProvider,
                $socialAccountProviderUserId
            );
        }

        return false;
    }

    /**
     * @param string $key
     * @return string
     */
    public static function generateApiToken(string $key): string
    {
        $secret = env('APP_SECRET', '1234');

        return md5($secret . $key . microtime());
    }
}