<?php

namespace App\Repository;

use App\DB\DB;

class SocialAccountsRepository
{
    public const TABLE_NAME = 'social_accounts';
    public const PROVIDER_GOOGLE = 'google';

    /**
     * @param int $userId
     * @param string $socialAccountProvider
     * @param string $socialAccountProviderUserId
     * @return bool
     */
    public static function createNewSocialAccount(
        int $userId,
        string $socialAccountProvider,
        string $socialAccountProviderUserId
    ): bool {
        $tableName = static::TABLE_NAME;
        $sql = "
            INSERT INTO $tableName
            (`user_id`, `provider_user_id`, `provider`, `created_at`, `updated_at`)
            VALUES
            (:user_id, :provider_user_id, :provider, NOW(), NOW())
        ";
        $params = [
            'user_id' => $userId,
            'provider_user_id' => $socialAccountProviderUserId,
            'provider' => $socialAccountProvider
        ];
        $stmt = DB::execute($sql, $params);

        return $stmt !== null;
    }
}