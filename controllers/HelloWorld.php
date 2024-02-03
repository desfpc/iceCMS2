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
use iceCMS2\Logger\LoggerFactory;
use iceCMS2\Tools\Exception;

class HelloWorld extends AbstractController implements ControllerInterface
{
    /** @var string Site Page Title */
    public string $title = 'Hello World!';

    /** @var bool Use vendor layout */
    public bool $vendorLayout = true;

    /**
     * Default main method - only render default template
     *
     * @throws Exception
     */
    public function main(): void
    {
        $this->breadcrumbs = [
            ['title' => 'Main', 'url' => '/'],
            ['title' => 'Hello World', 'url' => '/hello-world/'],
        ];

        LoggerFactory::log('debug', $this->breadcrumbs);

        $this->renderTemplate('main');
    }
}