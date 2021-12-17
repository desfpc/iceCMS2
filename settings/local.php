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
    'path' => 'text/path',
    'template' => 'ice',
    'dev' => true,
    'secret' => 'verySecretSecret',
    'db' => [
        'type' => 'MySQL',
        'name' => 'icecms2',
        'host' => '127.0.0.1',
        'port' => '3306',
        'login' => 'root',
        'pass' => 'root',
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
        'primary_domain' => 'icecms2',
        'redirect_to_primary_domain' => true,
        'language_subdomain' => true,
    ],
    'cache' => [
        'use_redis' => true,
        'redis_host' => '127.0.0.1',
        'redis_port' => 6379,
    ],
    'routes' => [
        '404' => '404',
        '500' => '500',
    ],
];