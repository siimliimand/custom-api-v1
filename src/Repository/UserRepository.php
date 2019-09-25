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
          FROM $usersTableName as u
          JOIN $socialAccountsTableName as sa ON sa.user_id = u.id
          JOIN $statusTableName as us ON u.status_id = us.id
         WHERE sa.provider_user_id = :provider_user_id
           AND sa.provider = :provider 
           AND us.code = :status
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
}