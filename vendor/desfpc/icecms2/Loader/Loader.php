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
use iceCMS2\Tools\Exception;

class Loader
{
    public Settings $settings;
    public Routing $routing;
    public ControllerInterface $controller;

    /**
     * Loader class constructor
     *
     * @param array $settings Settings array
     * @throws Exception
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
     * @param ?string $controllerName Controller name for loading or null for use Routing data
     * @param ?string $controllerMethod Controller method name for loading or null fot use Routing data
     * @throws Exception
     */
    public function loadController(?string $controllerName = null, ?string $controllerMethod = null, bool $putSettings = true): void
    {
        // including controller file
        if (is_null($controllerName)) {
            $controllerName = $this->routing->route['controller'];
        } else {
            $this->routing->route['controller'] = $controllerName;
        }

        if (is_null($controllerMethod)) {
            $controllerMethod = $this->routing->route['controllerMethod'];
        } else {
            $this->routing->route['controllerMethod'] = $controllerMethod;
        }

        $useVendor = $this->routing->route['useVendor'] ?? false;
        $controllerNameForClass = str_replace(DIRECTORY_SEPARATOR, '\\', $controllerName);

        if ($useVendor) {
            $controllerFile = $this->settings->path . 'controllers' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $controllerName . '.php';
            $controllerClassName = 'app\Controllers\vendor\\' . $controllerNameForClass;
        } else {
            $controllerFile = $this->settings->path . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
            $controllerClassName = 'app\Controllers\\' . $controllerNameForClass;
        }

        if (!include_once ($controllerFile)){
            throw new Exception('Can\'t load controller file: ' . $controllerFile);
        } else {

            if (!class_exists($controllerClassName)) {
                throw new Exception('Class not found: ' . $controllerClassName.'; ');
            } else {
                if (!$putSettings) {
                    $this->controller = new $controllerClassName($this->routing);
                } else {
                    $this->controller = new $controllerClassName($this->routing, $this->settings);
                }
            }
        }

        if (!method_exists($this->controller, $controllerMethod)) {
            throw new Exception('Can\'t run controller method: ' . $controllerMethod . ' not exist');
        }

        try {
            $this->controller->$controllerMethod();
        } catch (\Exception $e) {
            throw new Exception('Can\'t run controller method: ' . $controllerMethod . '; ' . $e->getMessage());
        }
    }
}