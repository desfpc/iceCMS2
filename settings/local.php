<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Local (dev) settings
 */

$settings = [
    'path' => str_replace('settings', '', dirname(__FILE__)),
    'template' => 'ice',
    'dev' => true,
    'secret' => 'verySecretSecret',
    'db' => [
        'type' => 'MySQL',
        'name' => 'ice2',
        'host' => '127.0.0.1',
        'port' => '3306',
        'login' => 'root',
        'pass' => 'localRoot',
        'encoding' => 'UTF8',
    ],
    'dbTest' => [
        'type' => 'MySQL',
        'name' => 'ice2_test',
        'host' => '127.0.0.1',
        'port' => '3306',
        'login' => 'root',
        'pass' => 'localRoot',
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
        '500' => ['controller' => 'ServerErrors', 'method' => 'serverError', 'useVendor' => true],
        '404' => ['controller' => 'ServerErrors', 'method' => 'serverError', 'useVendor' => true],
        '' => ['controller' => 'Main', 'method' => 'main', 'useVendor' => true],
        'user' => ['controller' => 'User', 'method' => 'main', 'useVendor' => true],
    ],
];