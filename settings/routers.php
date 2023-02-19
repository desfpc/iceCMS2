<?php
$routers = [
    '500' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '404' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '' => ['controller' => 'Main', 'controllerMethod' => 'main', 'useVendor' => true],
    'authorize' => ['controller' => 'Authorize', 'controllerMethod' => 'main', 'useVendor' => true],
    'registration' => ['controller' => 'Authorize', 'controllerMethod' => 'registration', 'useVendor' => true],
    'reset-password' => ['controller' => 'Authorize', 'controllerMethod' => 'resetPassword', 'useVendor' => true],
    'admin' => ['controller' => 'Admin', 'controllerMethod' => 'main', 'useVendor' => true],
    'hello-world' => ['controller' => 'HelloWorld', 'controllerMethod' => 'main', 'useVendor' => false],
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