<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Console app
 * @var array $settings
 */

require_once './vendor/autoload.php';
require_once './settings/settingsSelector.php';

use iceCMS2\Cli\App;

$app = new App($settings, $argv);