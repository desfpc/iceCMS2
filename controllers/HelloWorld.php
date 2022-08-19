<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * HelloWorld App Controller Class
 */

namespace app\Controllers;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;

class HelloWorld extends AbstractController implements ControllerInterface
{
    /** @var string Site Page Title */
    public string $title = 'Hello World!';

    /** @var bool Use vendor layout */
    public bool $vendorLayout = true;
}