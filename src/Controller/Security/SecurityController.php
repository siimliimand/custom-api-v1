<?php

namespace App\Controller\Security;

use App\Configuration\Configuration;
use App\Controller\AbstractController;
use App\Exception\InvalidGoogleIdTokenException;
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
    private const USER = 'user';

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
            return [
                static::ID => $ticket[static::SUB],
                static::NAME => $ticket[static::NAME],
                static::EMAIL => $ticket[static::EMAIL],
                static::AVATAR => $ticket[static::PICTURE],
                static::USER => [
                    'name' => 'Test User' // TODO: Add user data
                ]
            ];
        }

        throw new InvalidGoogleIdTokenException(
            translate('messages.error.invalid_google_id_token')
        );
    }
}