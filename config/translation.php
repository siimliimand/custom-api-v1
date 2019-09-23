<?php

use App\Exception\InvalidGoogleIdTokenException;
use App\Exception\InvalidRouteException;
use App\Exception\UnauthorizedException;

return [
    'messages' => [
        'error' => [
            'invalid_route' => InvalidRouteException::MESSAGE,
            'invalid_method' => 'You must make %s request',
            'invalid_google_id_token' => InvalidGoogleIdTokenException::MESSAGE,
            'unauthorized' => UnauthorizedException::MESSAGE
        ]
    ]
];