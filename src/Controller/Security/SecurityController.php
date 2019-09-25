<?php

namespace App\Controller\Security;

use App\Configuration\Configuration;
use App\Controller\AbstractController;
use App\Exception\InvalidGoogleIdTokenException;
use App\Repository\SocialAccountsRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Google_Client;
use App\RequestHandler\Request;

class SecurityController extends AbstractController
{

    public const ROUTE_NAME = 'security';
    public const ROUTE_PART_LOGIN_WITH_GOOGLE = 'login-with-google';
    public const ACTION_LOGIN_WITH_GOOGLE = 'loginWithGoogle';

    private const ID ='id';
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
        if ($idToken === null) {
            throw new InvalidGoogleIdTokenException(
                translate('translation.messages.error.invalid_google_id_token')
            );
        }

        $client = new Google_Client();
        $client->setApplicationName(Configuration::get('app.google.app_name'));
        $client->setClientId(Configuration::get('app.google.client_id'));

        $ticket = $client->verifyIdToken($idToken);
        if ($ticket && is_array($ticket)) {
            $user = UserRepository::getUserByGoogleId($ticket[static::SUB]);
            if ($user === null) {
                if (UserRepository::createNewUser(
                    $ticket[static::NAME],
                    $ticket[static::EMAIL],
                    UserRepository::generateApiToken($ticket[static::SUB]),
                    StatusRepository::STATUS_ACTIVE,
                    SocialAccountsRepository::PROVIDER_GOOGLE,
                    $ticket[static::SUB]
                )) {
                    $user = UserRepository::getUserByGoogleId($ticket[static::SUB]);
                    if($user === null) {
                        return [];
                    }
                } else {
                    return [];
                }
            }

            return [
                static::AVATAR => $ticket[static::PICTURE],
                'name' => $user['name'],
                'email' => $user['email'],
                'api_token' => $user['api_token']
            ];
        }

        throw new InvalidGoogleIdTokenException(
            translate('messages.error.invalid_google_id_token')
        );
    }
}