<?php

use App\Configuration\RoutesConfigurationInterface;
use App\Controller\Security\SecurityController;

return [
    RoutesConfigurationInterface::ROUTES => [
        SecurityController::ROUTE_PART_LOGIN_WITH_GOOGLE => [
            RoutesConfigurationInterface::DATA => [
                RoutesConfigurationInterface::CONTROLLER => SecurityController::class,
                RoutesConfigurationInterface::ACTION => SecurityController::ACTION_LOGIN_WITH_GOOGLE,
                RoutesConfigurationInterface::METHOD => RoutesConfigurationInterface::METHOD_POST
            ]
        ]
    ]
];