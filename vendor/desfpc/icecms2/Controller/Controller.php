<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Controller class
 */

namespace iceCMS2\Controller;

use desfpc\Visualijoper\Visualijoper;
use iceCMS2\Routing\Routing;
use iceCMS2\Settings\Settings;

abstract class Controller implements ControllerInterface
{
    /** @var Settings|null Project settings */
    public ?Settings $settings = null;

    /** @var Routing|null Routing data */
    public ?Routing $routing = null;

    /** @var string Template layout */
    public string $layout = 'default';

    /** @var string[] JS files to load */
    public array $jsFiles = ['/js/ice.js'];

    /** @var string[] CSS files to load */
    public array $cssFiles = ['/css/ice.css'];

    /** @var string JS code for Document Ready */
    public string $jsReady = '';

    /** @var string Full template file path for including */
    protected string $_fullTemplatePath = '';

    /** Class constructor */
    public function __construct(?Routing $routing, ?Settings $settings)
    {
        $this->routing = $routing;
        $this->settings = $settings;
    }

    /** Default main method - only render default template */
    public function main(): void
    {
        $this->renderTemplate('main');
    }

    /**
     * Render (print) template
     *
     * @param ?string $template Template name
     * @param bool $isFullTemplatePatch If "true", then the $template parameter contains the full template file path
     */
    public function renderTemplate(?string $template = null, bool $isFullTemplatePatch = false): void
    {
        if (is_null($template)) {
            $dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
            $template = isset($dbt[1]['function']) ? $dbt[1]['function'] : 'main';
        }
        Visualijoper::visualijop($this->_getFullTemplatePath($template));
        Visualijoper::visualijop($this->_getFullLayoutPath());

        if ($isFullTemplatePatch) {
            $this->_fullTemplatePath = $template;
        } else {
            $this->_fullTemplatePath = $this->_getFullTemplatePath($template);
        }

        try {
            ob_start();
            require($this->_getFullLayoutPath());
            echo ob_get_contents();
            ob_end_clean();
        } catch (\Exception $e) {
            throw new \Exception('Can\'t render template: ' . $e->getMessage());
        }
    }

    /**
     * Get full layout file path
     *
     * @return string
     */
    protected function _getFullLayoutPath(): string
    {
        return $this->settings->path . 'templates' . DIRECTORY_SEPARATOR . $this->settings->template
            . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->layout . '.php';
    }

    /**
     * Get full template file path
     *
     * @param string $template
     * @return string
     */
    protected function _getFullTemplatePath(string $template): string
    {
        return $this->settings->path . 'templates' . DIRECTORY_SEPARATOR . $this->settings->template
            . DIRECTORY_SEPARATOR . $this->routing->route['controller'] . DIRECTORY_SEPARATOR . $template . '.php';
    }
}