<?php

$routers = [
    //Error pages
    '500' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
    '404' => ['controller' => 'ServerErrors', 'controllerMethod' => 'notFound', 'useVendor' => true],

    //Site pages
    '' => ['controller' => 'Main', 'controllerMethod' => 'main', 'useVendor' => true],
    'authorize' => ['controller' => 'Authorize', 'controllerMethod' => 'main', 'useVendor' => true],
    'registration' => ['controller' => 'Authorize', 'controllerMethod' => 'registration', 'useVendor' => true],
    'reset-password' => ['controller' => 'Authorize', 'controllerMethod' => 'resetPassword', 'useVendor' => true],
    'exit' => ['controller' => 'Authorize', 'controllerMethod' => 'exit', 'useVendor' => true],
    'profile-settings' => ['controller' => 'Authorize', 'controllerMethod' => 'profile', 'useVendor' => true],
    'profile/$id' => ['controller' => 'Profile', 'controllerMethod' => 'user', 'useVendor' => true],
    'profile' => ['controller' => 'Profile', 'controllerMethod' => 'main', 'useVendor' => true],

    //No vendor controllers
    'hello-world' => ['controller' => 'HelloWorld', 'controllerMethod' => 'main', 'useVendor' => false],

    //Admin pages
    'admin/materials' => ['controller' => 'AdminMaterials', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/file/$id/edit' => ['controller' => 'AdminFiles', 'controllerMethod' => 'edit', 'useVendor' => true],
    'admin/files' => ['controller' => 'AdminFiles', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/image-sizes' => ['controller' => 'AdminImageSizes', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/user/$id/edit' => ['controller' => 'AdminUsers', 'controllerMethod' => 'edit', 'useVendor' => true],
    'admin/users' => ['controller' => 'AdminUsers', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/caches/clear-php' => ['controller' => 'AdminCaches', 'controllerMethod' => 'clearPHPCaches', 'useVendor' => true],
    'admin/caches/clear' => ['controller' => 'AdminCaches', 'controllerMethod' => 'clearAllCaches', 'useVendor' => true],
    'admin/caches' => ['controller' => 'AdminCaches', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/settings' => ['controller' => 'AdminSettings', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/logs/clear-all-logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'clearAllLogs', 'useVendor' => true],
    'admin/logs/clear-period-logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'clearOnPeriodLogs', 'useVendor' => true],
    'admin/logs' => ['controller' => 'AdminLogs', 'controllerMethod' => 'main', 'useVendor' => true],
    'admin/queues' => ['controller' => 'AdminQueues', 'controllerMethod' => 'main', 'useVendor' => false],
    'admin' => ['controller' => 'Admin', 'controllerMethod' => 'main', 'useVendor' => true],


    //API for site pages (Token authorization) TODO move to separate file
    'api/v1/users' => [ //Users list
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'list',
        'useVendor' => true
    ],
    'api/v1/profile/auth' => [ //Auth user
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'auth',
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
    'api/v1/user/$id' => [ //User by ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],
    'api/v1/user' => [ //User by ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],
    'api/v1/get-file-url/$id' => [ //File url by File ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getFileUrl',
        'useVendor' => true
    ],
    'api/v1/common/locales' => [ //Get locales
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getLocales',
        'useVendor' => true
    ],
    'api/v1/common/statuses' => [ //Get statuses
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getStatuses',
        'useVendor' => true
    ],
    'api/v1/common/roles' => [ //Get roles
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getRoles',
        'useVendor' => true
    ],
    'api/v1/common/bools' => [ //Get bools
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getBools',
        'useVendor' => true
    ],
    'api/v1/common/sexes' => [ //Get sexes
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getSexes',
        'useVendor' => true
    ],
    'api/v1/common/text' => [ //Get text from translates
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'Common',
        'controllerMethod' => 'getText',
        'useVendor' => true
    ],

    //API for admin pages (Session authorization) TODO move to separate file
    'api/v1/admin/files' => [ //Files list
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminFiles',
        'controllerMethod' => 'list',
        'useVendor' => true
    ],
    'api/v1/admin/file/$id/delete' => [ //Delete file by ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminFiles',
        'controllerMethod' => 'delete',
        'useVendor' => true
    ],
    'api/v1/admin/file/$id/edit-prop' => [ //Edit file property by file ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminFiles',
        'controllerMethod' => 'editProperty',
        'useVendor' => true
    ],
    'api/v1/admin/file/$id/edit' => [ //Edit file (all form) by ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminFiles',
        'controllerMethod' => 'edit',
        'useVendor' => true
    ],
    'api/v1/admin/file/$id' => [ //File by ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminFiles',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],
    'api/v1/admin/image-sizes' => [ //Image-files list
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminImageSizes',
        'controllerMethod' => 'list',
        'useVendor' => true
    ],
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
    'api/v1/admin/user/$id/avatar' => [ //Upload avatar in admin
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'uploadAvatar',
        'useVendor' => true
    ],
    'api/v1/admin/user/$id/password' => [ //Edit user password by ID
        'method' => 'POST',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'password',
        'useVendor' => true
    ],
    'api/v1/admin/user/$id' => [ //User by ID
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminUser',
        'controllerMethod' => 'get',
        'useVendor' => true
    ],
    'api/v1/admin/get-logs/$nameFile' => [ //Get filename from logs folder
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminLogs',
        'controllerMethod' => 'getLogByNameFile',
        'useVendor' => true
    ],
    'api/v1/admin/get-db-logs/$aliasAndCreateTime' => [ //Get filename from logs DB
        'method' => 'GET',
        'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'AdminLogs',
        'controllerMethod' => 'getLogByAliasAndCreateTime',
        'useVendor' => true
    ],
];