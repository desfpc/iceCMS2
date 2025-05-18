<?php

$routers = [
    '' => ['controller' => 'Tests', 'controllerMethod' => 'main', 'useVendor' => false],
    'tests' => [ //Users list
        'method' => 'GET',
        'controller' => 'Tests',
        'controllerMethod' => 'tests',
        'useVendor' => false
    ],
];