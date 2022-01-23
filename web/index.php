<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Start point of Your Best Application
 */

//Composer auto-load project classes
require_once '../vendor/autoload.php';

/** @var array $settings Settings array from settingsSelector.php */
require_once '../settings/settingsSelector.php';

use iceCMS2\Loader\Loader;

$site = new Loader($settings);
$site->loadController();

echo 'Hello World!';