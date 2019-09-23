<?php

namespace App\Exception;

class UnauthorizedException extends AbstractException
{
    public const MESSAGE = 'You are not authorized to access this page!';
}