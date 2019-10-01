<?php

use App\Configuration\RoutesConfigurationInterface;
use App\Controller\TestController;

return [
    RoutesConfigurationInterface::DATA => [
        RoutesConfigurationInterface::CONTROLLER => TestController::class,
        RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_INDEX,
        RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET,
        RoutesConfigurationInterface::ACCESS => RoutesConfigurationInterface::ACCESS_PUBLIC
    ],
    RoutesConfigurationInterface::ROUTES => [
        'test' => [
            RoutesConfigurationInterface::DATA => [
                RoutesConfigurationInterface::CONTROLLER => TestController::class,
                RoutesConfigurationInterface::ACTION => 'test',
                RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET,
                RoutesConfigurationInterface::ACCESS => RoutesConfigurationInterface::ACCESS_PRIVATE
            ]
        ],
        RoutesConfigurationInterface::REGEX => [
            RoutesConfigurationInterface::REGEX_DATA => [
                RoutesConfigurationInterface::NAME => RoutesConfigurationInterface::PARAMETER_ID,
                RoutesConfigurationInterface::VALUE => RoutesConfigurationInterface::REGEX_UUID
            ],
            RoutesConfigurationInterface::DATA => [
                RoutesConfigurationInterface::CONTROLLER => TestController::class,
                RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_SHOW,
                RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET,
                RoutesConfigurationInterface::ACCESS => RoutesConfigurationInterface::ACCESS_PRIVATE
            ],
            RoutesConfigurationInterface::ROUTES => [
                RoutesConfigurationInterface::ACTION_EDIT => [
                    RoutesConfigurationInterface::DATA => [
                        RoutesConfigurationInterface::CONTROLLER => TestController::class,
                        RoutesConfigurationInterface::ACTION => RoutesConfigurationInterface::ACTION_EDIT,
                        RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET,
                        RoutesConfigurationInterface::ACCESS => RoutesConfigurationInterface::ACCESS_PRIVATE
                    ]
                ],
                'secure' => [
                    RoutesConfigurationInterface::ROUTES => [
                        RoutesConfigurationInterface::REGEX => [
                            RoutesConfigurationInterface::REGEX_DATA => [
                                RoutesConfigurationInterface::NAME => RoutesConfigurationInterface::PARAMETER_TOKEN,
                                RoutesConfigurationInterface::VALUE => RoutesConfigurationInterface::REGEX_MD5
                            ],
                            RoutesConfigurationInterface::DATA => [
                                RoutesConfigurationInterface::CONTROLLER => TestController::class,
                                RoutesConfigurationInterface::ACTION => 'secure',
                                RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_GET,
                                RoutesConfigurationInterface::ACCESS => RoutesConfigurationInterface::ACCESS_PRIVATE
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];