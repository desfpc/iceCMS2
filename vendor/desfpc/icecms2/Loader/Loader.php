<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Loader class
 */

namespace iceCMS2\Loader;

use iceCMS2\Settings\Settings;
use iceCMS2\Routing\Routing;
use iceCMS2\Controller\ControllerInterface;
use desfpc\Visualijoper\Visualijoper;

class Loader
{
    public Settings $settings;
    public Routing $routing;
    public ControllerInterface $controller;

    /**
     * Loader class constructor
     *
     * @param array $settings Settings array
     */
    public function __construct(array $settings)
    {
        $this->settings = new Settings($settings);
        $this->routing = new Routing();
        $this->routing->parseURL();
        $this->routing->getRoute($this->settings);
    }

    /**
     * Load conrtoller from $controllerName param or $this->routing data
     *
     * @param ?string $controllerName Contoller name for loading or null for use Routing data
     * @param ?string $controllerMethod Controller method name for loading or null fot use Routing data
     */
    public function loadController(?string $controllerName = null, ?string $controllerMethod = null): void
    {
        if (is_null($controllerName)) {
            $controllerName = $this->routing->route['controller'];
        }

        if (is_null($controllerMethod)) {
            $controllerMethod = $this->routing->route['method'];
        }

        $controllerFile = $this->settings->path . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
        require_once ($controllerFile);
        $controllerClassName = 'app\Controllers\\' . $controllerName;
        $this->controller = new $controllerClassName();
        
        Visualijoper::visualijop($this->routing);
        Visualijoper::visualijop($this->settings);
        Visualijoper::visualijop($this->controller);
    }
}