<?php

namespace App\Exception;

class TranslationNotFoundException extends  AbstractException
{
    public const MESSAGE = 'Translation not found';
}