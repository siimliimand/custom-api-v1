<?php

use App\Configuration\RoutesConfigurationInterface;
use App\Controller\DefaultController;

return [
    '/' => [
        RoutesConfigurationInterface::DATA => [
            RoutesConfigurationInterface::CONTROLLER => DefaultController::class,
            RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_INDEX,
            RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET,
            RoutesConfigurationInterface::ACCESS => RoutesConfigurationInterface::ACCESS_PUBLIC
        ],
        RoutesConfigurationInterface::ROUTES => [
            RoutesConfigurationInterface::REGEX => [
                RoutesConfigurationInterface::REGEX_DATA => [
                    RoutesConfigurationInterface::NAME => RoutesConfigurationInterface::PARAMETER_LANGUAGE_CODE,
                    RoutesConfigurationInterface::VALUE => RoutesConfigurationInterface::REGEX_LANGUAGE_CODE
                ],
                RoutesConfigurationInterface::ROUTES => [
                    'test' => require CONFIG_PATH . 'routes/test.php',
                    'security' => require CONFIG_PATH . 'routes/security.php'
                ]
            ]
        ]
    ]
];