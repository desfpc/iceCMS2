<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default example settings
 */

$settings = [
    'path' => str_replace('settings', '', dirname(__FILE__)),
    'template' => 'ice',
    'dev' => true,
    'secret' => 'verySecretSecret',
    'db' => [
        'type' => 'MySQL',
        'name' => 'icecms2',
        'host' => '127.0.0.1',
        'port' => '3306',
        'login' => 'login',
        'pass' => 'pass',
        'encoding' => 'UTF8',
    ],
    'dbTest' => [
        'type' => 'MySQL',
        'name' => 'icecms2_test',
        'host' => '127.0.0.1',
        'port' => '3306',
        'login' => 'login',
        'pass' => 'pass',
        'encoding' => 'UTF8',
    ],
    'email' => [
        'mail' => 'test@domain',
        'port' => '465',
        'signature' => 'Test mail system',
        'pass' => 'testPass',
        'smtp' => 'smtp.domain',
    ],
    'sms' => null,
    'site' => [
        'title' => 'IceCMS2 Great Site',
        'primaryDomain' => 'icecms2',
        'redirectToPrimaryDomain' => true,
        'languageSubdomain' => true,
        'cssScriptsVersion' => '1',
        'jsScriptsVersion' => '1',
    ],
    'cache' => [
        'useRedis' => true,
        'redisHost' => '127.0.0.1',
        'redisPort' => 6379,
    ],
    'routes' => [
        '500' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
        '404' => ['controller' => 'ServerErrors', 'controllerMethod' => 'serverError', 'useVendor' => true],
        '' => ['controller' => 'Main', 'controllerMethod' => 'main', 'useVendor' => true],
        'admin' => ['controller' => 'Admin', 'controllerMethod' => 'main', 'useVendor' => true],
        'api/v1/users' => [
            'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
            'controllerMethod' => 'list',
            'useVendor' => true
        ],
        'api/v1/user/$id' => [
            'controller' => 'api' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'User',
            'controllerMethod' => 'get',
            'useVendor' => true
        ],
    ],
];