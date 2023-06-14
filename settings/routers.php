<?php
$routers = [
    '500' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '404' => ['controller' => 'ServerErrors', 'controllerMethod' => 'notFound', 'useVendor' => true],

    '' => ['controller' => 'Main', 'controllerMethod' => 'main', 'useVendor' => true],
    'authorize' => ['controller' => 'Authorize', 'controllerMethod' => 'main', 'useVendor' => true],
    'registration' => ['controller' => 'Authorize', 'controllerMethod' => 'registration', 'useVendor' => true],
    'reset-password' => ['controller' => 'Authorize', 'controllerMethod' => 'resetPassword', 'useVendor' => true],
    'exit' => ['controller' => 'Authorize', 'controllerMethod' => 'exit', 'useVendor' => true],
    'profile' => ['controller' => 'Authorize', 'controllerMethod' => 'profile', 'useVendor' => true],

    'admin/materials' => ['controller' => 'AdminMaterials', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/files' => ['controller' => 'AdminFiles', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/users' => ['controller' => 'AdminUsers', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/caches' => ['controller' => 'AdminCaches', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/settings' => ['controller' => 'AdminSettings', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'main', 'useVendor' => true],

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
    'api/v1/profile/avatar' => [
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'uploadAvatar',
        'useVendor' => true
    ],
];