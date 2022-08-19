<?php
$routers = [
    '500' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '404' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '' => ['controller' => 'Main', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin' => ['controller' => 'Admin', 'controllerMethod' => 'main', 'useVendor' => true],
    'api/v1/users' => [
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'list',
        'useVendor' => true
    ],
    'api/v1/user/$id' => [
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],
];