<?php

use App\Configuration\RoutesConfigurationInterface;
use App\Controller\TestController;

return [
    RoutesConfigurationInterface::DATA => [
        RoutesConfigurationInterface::CONTROLLER => TestController::class,
        RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_INDEX,
        RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET
    ],
    RoutesConfigurationInterface::ROUTES => [
        RoutesConfigurationInterface::REGEX => [
            RoutesConfigurationInterface::REGEX_DATA => [
                RoutesConfigurationInterface::NAME => RoutesConfigurationInterface::PARAMETER_ID,
                RoutesConfigurationInterface::VALUE => RoutesConfigurationInterface::REGEX_UUID
            ],
            RoutesConfigurationInterface::DATA => [
                RoutesConfigurationInterface::CONTROLLER => TestController::class,
                RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_SHOW,
                RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET
            ],
            RoutesConfigurationInterface::ROUTES => [
                RoutesConfigurationInterface::ACTION_EDIT => [
                    RoutesConfigurationInterface::DATA => [
                        RoutesConfigurationInterface::CONTROLLER => TestController::class,
                        RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_EDIT,
                        RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET
                    ]
                ],
                'secure' => [
                    RoutesConfigurationInterface::ROUTES => [
                        RoutesConfigurationInterface::REGEX => [
                            RoutesConfigurationInterface::REGEX_DATA => [
                                RoutesConfigurationInterface::NAME => RoutesConfigurationInterface::PARAMETER_TOKEN,
                                RoutesConfigurationInterface::VALUE => RoutesConfigurationInterface::REGEX_UUID
                            ],
                            RoutesConfigurationInterface::DATA => [
                                RoutesConfigurationInterface::CONTROLLER => TestController::class,
                                RoutesConfigurationInterface::ACTION => 'secure',
                                RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];