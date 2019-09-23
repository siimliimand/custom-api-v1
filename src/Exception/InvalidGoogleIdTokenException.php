<?php

namespace App\Exception;

class InvalidGoogleIdTokenException extends AbstractException
{
    public const MESSAGE = 'Invalid Google ID token';
}