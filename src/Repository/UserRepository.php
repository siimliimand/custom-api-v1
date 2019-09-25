<?php

namespace App\Repository;

use App\DB\DB;

class UserRepository
{
    public const TABLE_NAME = 'users';

    /**
     * @param string $googleId
     * @return array|null
     */
    public static function getUserByGoogleId(string $googleId): ?array
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

        return md5($secret . $key);
    }
}