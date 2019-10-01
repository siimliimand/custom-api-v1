<?php

namespace App\Configuration;

use App\Controller\ControllerInterface;

interface RoutesConfigurationInterface
{
    public const PREFIX = 'routes.';
    public const DATA = '_data';
    public const ROUTES = '_routes';
    public const REGEX = '_regex';
    public const REGEX_DATA = '_regex_data';
    public const CONTROLLER = '_controller';
    public const ACTION = '_action';
    public const NAME = '_name';
    public const VALUE = '_value';
    public const METHOD = '_method';
    public const PARAMETERS = '_parameters';
    public const ACCESS = '_access';
    public const ACCESS_PRIVATE = 'private';
    public const ACCESS_PUBLIC = 'public';

    public const ACTION_INDEX = 'index';
    public const ACTION_SHOW = 'show';
    public const ACTION_EDIT = 'edit';

    public const PARAMETER_ID = 'id';
    public const PARAMETER_TOKEN = 'token';
    public const PARAMETER_LANGUAGE_CODE = 'language';

    public const METHOD_GET = ControllerInterface::METHOD_GET;
    public const METHOD_POST = ControllerInterface::METHOD_POST;
    public const METHOD_PUT = ControllerInterface::METHOD_PUT;
    public const METHOD_DELETE = ControllerInterface::METHOD_DELETE;
    public const METHOD_OPTIONS = ControllerInterface::METHOD_OPTIONS;

    public const REGEX_LANGUAGE_CODE = '/^[a-z]{2}$/i';
    public const REGEX_UUID = '/^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/i';
    public const REGEX_MD5 = '/^[a-f0-9]{32}$/i';
}