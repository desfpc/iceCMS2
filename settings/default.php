<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default example settings
 */

/** @var array $routers */
require_once('routers.php');

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
        'localeSubdomain' => true,
        'cssScriptsVersion' => '1',
        'jsScriptsVersion' => '1',
    ],
    'locales' => [
        'en', 'ru', 'ge', 'sr',
    ],
    'locale' => 'en',
    'logs' => [
        'period' => 'month',
        'periodClear' => 'day',
        'type' => 'db',
    ],
    'cache' => [
        'useRedis' => true,
        'redisHost' => '127.0.0.1',
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
];