<?php

$routers = [
    '' => ['controller' => 'Tests', 'controllerMethod' => 'main', 'useVendor' => false],
    'tests' => [
        'method' => 'GET',
        'controller' => 'Tests',
        'controllerMethod' => 'tests',
        'useVendor' => false
    ],
    'test/$id/test/$subid/test/$subsubid' => [
        'method' => 'GET',
        'controller' => 'Tests',
        'controllerMethod' => 'threetest',
        'useVendor' => false
    ],
    'test/$id/test/$subid' => [
        'method' => 'GET',
        'controller' => 'Tests',
        'controllerMethod' => 'twotest',
        'useVendor' => false
    ],
    'test/$id' => [
        'method' => 'GET',
        'controller' => 'Tests',
        'controllerMethod' => 'onetest',
        'useVendor' => false
    ],
];