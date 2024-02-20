<?php

$routers = [
    //Error pages
    '500' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '404' => ['controller' => 'ServerErrors', 'controllerMethod' => 'notFound', 'useVendor' => true],

    //Main pages
    '' => ['controller' => 'Main', 'controllerMethod' => 'main', 'useVendor' => true],
    'authorize' => ['controller' => 'Authorize', 'controllerMethod' => 'main', 'useVendor' => true],
    'registration' => ['controller' => 'Authorize', 'controllerMethod' => 'registration', 'useVendor' => true],
    'reset-password' => ['controller' => 'Authorize', 'controllerMethod' => 'resetPassword', 'useVendor' => true],
    'exit' => ['controller' => 'Authorize', 'controllerMethod' => 'exit', 'useVendor' => true],
    'profile' => ['controller' => 'Authorize', 'controllerMethod' => 'profile', 'useVendor' => true],

    //No vendor controllers
    'hello-world' => ['controller' => 'HelloWorld', 'controllerMethod' => 'main', 'useVendor' => false],

    //Admin pages
    'admin/materials' => ['controller' => 'AdminMaterials', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/files' => ['controller' => 'AdminFiles', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/user/$id/edit' => ['controller' => 'AdminUsers', 'controllerMethod' => 'edit', 'useVendor' => true],
    'admin/users' => ['controller' => 'AdminUsers', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/caches/clear-php' => ['controller' => 'AdminCaches', 'controllerMethod' => 'clearPHPCaches', 'useVendor' => true],
    'admin/caches/clear' => ['controller' => 'AdminCaches', 'controllerMethod' => 'clearAllCaches', 'useVendor' => true],
    'admin/caches' => ['controller' => 'AdminCaches', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/settings' => ['controller' => 'AdminSettings', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/logs/clear-all-logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'clearAllLogs', 'useVendor' => true],
    'admin/logs/clear-period-logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'clearOnPeriodLogs', 'useVendor' => true],
    'admin/logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin' => ['controller' => 'Admin', 'controllerMethod' => 'main', 'useVendor' => true],

    //API for admin pages (Session authorization)
    'api/v1/admin/users' => [ //Users list
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'list',
        'useVendor' => true
    ],

    'api/v1/admin/user/$id/delete' => [ //Delete user by ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'delete',
        'useVendor' => true
    ],

    'api/v1/admin/user/$id/edit-prop' => [ //Edit user property by user ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'editProperty',
        'useVendor' => true
    ],

    'api/v1/admin/user/$id/edit' => [ //Edit user (all form) by ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'edit',
        'useVendor' => true
    ],

    'api/v1/admin/user/$id' => [ //User by ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],

    //API for site pages (Session authorization)
    'api/v1/users' => [ //Users list
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'list',
        'useVendor' => true
    ],
    'api/v1/friends' => [ //Friends list
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'friends',
        'useVendor' => true
    ],
    'api/v1/user/$id' => [ //User by ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],
    'api/v1/profile/avatar' => [ //Upload avatar
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'uploadAvatar',
        'useVendor' => true
    ],
    'api/v1/profile/update' => [ //Update profile
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'updateProfile',
        'useVendor' => true
    ],
    'api/v1/profile/change-password' => [ //Change password
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'changePassword',
        'useVendor' => true
    ],
    'api/v1/get-logs/$nameFile' => [ //Get filename from logs folder
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminLogs',
        'controllerMethod' => 'getLogByNameFile',
        'useVendor' => true
    ],
    'api/v1/get-db-logs/$aliasAndCreateTime' => [ //Get filename from logs DB
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminLogs',
        'controllerMethod' => 'getLogByAliasAndCreateTime',
        'useVendor' => true
    ],
];