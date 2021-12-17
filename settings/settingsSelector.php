<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Settings selector
 * Select right settings file and require
 * Gitignore this file for CI/CD
 *
 * Put it on the server manually with the choice:
 * require_once('product.php');
 * //require_once('local.php');
 *
 * In local machine your choice would be:
 * //require_once('product.php');
 * require_once('local.php');
 */

//require_once('product.php');
require_once('local.php');