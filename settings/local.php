<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Local (dev) settings
 */

/** @var array $routers */
require('routers.php');

$settings = [
    'path' => str_replace('settings', '', dirname(__FILE__)),
    'template' => 'ice',
    'layoutUseVendor' => true,
    'dev' => true,
    'secret' => 'verySecretSecret',
    'db' => [
        'type' => 'MySQL',
        'name' => 'ice2',
        'host' => 'db-icecms',
        'port' => '3306',
        'login' => 'root',
        'pass' => 'localRoot',
        'encoding' => 'UTF8',
    ],
    'dbTest' => [
        'type' => 'MySQL',
        'name' => 'ice2_test',
        'host' => 'db-icecms',
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
        'redirectToPrimaryDomain' => false,
        'localeSubdomain' => false,
        'cssScriptsVersion' => '1',
        'jsScriptsVersion' => '1',
    ],
    'locales' => [
        'en', 'ru', 'ge', 'sr',
    ],
    'locale' => 'en',
    'defaultLocale' => 'en',
    'logs' => [
        'period' => 'month',
        'periodClear' => 'month',
        'type' => 'file',
    ],
    'cache' => [
        'useRedis' => true,
        'redisHost' => 'redis-icecms',
        'redisPort' => 6379,
        'redisDB' => 1,
    ],
    'routes' => $routers,
    'isUseCms' => true,
    'search' => [
        'type' => 'Elastic',
        'login' => 'elastic',
        'password' => 'MyPw123'
    ],
    'queue' => [
        'mysql' => [
            'host' => 'db-icecms',
            'type' => 'MySQL',
            'name' => 'ice2',
            'port' => '3306',
            'login' => 'root',
            'pass' => 'localRoot',
            'encoding' => 'UTF8',
            'clear_completed_task' => false,
        ],
        'redis' => [
            'host' => 'redis-icecms',
            'redisPort' => 6379,
            'redisDB' => 2,
            'clear_completed_task' => false,
        ],
        'default' => 'mysql'
    ],
];