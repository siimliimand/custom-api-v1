<?php

namespace App\Controller\Security;

use App\Controller\AbstractController;
use App\Exception\InvalidGoogleIdTokenException;
use App\Google\IdTokenVerifier;
use App\Repository\SocialAccountsRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\RequestHandler\Request;

class SecurityController extends AbstractController
{

    public const ROUTE_NAME = 'security';
    public const ROUTE_PART_LOGIN_WITH_GOOGLE = 'login-with-google';
    public const ACTION_LOGIN_WITH_GOOGLE = 'loginWithGoogle';

    private const SUB = 'sub';
    private const NAME = 'name';
    private const EMAIL = 'email';
    private const PICTURE = 'picture';
    private const AVATAR = 'avatar';

    /**
     * @param Request $request
     * @return array
     * @throws InvalidGoogleIdTokenException
     */
    public function loginWithGoogle(Request $request): array
    {
        $idToken = $request->request->get('id_token', null);

        // Verify Google id_token
        if ($idToken === null || IdTokenVerifier::verify($idToken) === false) {
            throw new InvalidGoogleIdTokenException(
                translate('messages.error.invalid_google_id_token')
            );
        }

        $payload = IdTokenVerifier::getPayload();
        if ($payload) {
            $apiToken = UserRepository::generateApiToken($payload[static::SUB]);
            $user = UserRepository::getUserByGoogleId($payload[static::SUB]);

            // Create user or update user api token
            if ($user === null && UserRepository::createNewUser(
                    $payload[static::NAME],
                    $payload[static::EMAIL],
                    $apiToken,
                    StatusRepository::STATUS_ACTIVE,
                    SocialAccountsRepository::PROVIDER_GOOGLE,
                    $payload[static::SUB]
                )) {
                $user = UserRepository::getUserByGoogleId($payload[static::SUB]);
            } else {
                UserRepository::updateApiToken($user['id'], $apiToken);
            }

            if ($user !== null) {
                $request->headers->set('X-API-KEY', $apiToken);

                return [
                    static::AVATAR => $payload[static::PICTURE],
                    'name' => $user['name'],
                    'email' => $user['email']
                ];
            }
        }

        throw new InvalidGoogleIdTokenException(
            translate('messages.error.invalid_google_id_token')
        );
    }
}