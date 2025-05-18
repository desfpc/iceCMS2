<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Start point of Your Best Application
 */

//Composer auto-load
require_once '../vendor/autoload.php';

//Variables Visualization class for debugging
require_once '../vendor/desfpc/visualijoper/src/Visualijoper.php';

/** @var array $settings Settings array from settingsSelector.php */
require_once '../settings/settingsSelector.php';

use iceCMS2\Loader\Loader;

try {
    $app = new Loader($settings);
    $app->loadController();
} catch (Exception $e) {
    if ($app instanceof Loader) {
        $app->setServerErrorsController();
        $app->loadController();
    } else {
        echo 'Server error';
    }
}