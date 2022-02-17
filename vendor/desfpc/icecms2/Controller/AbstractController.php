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

use iceCMS2\Routing\Routing;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\FlashVars;
use iceCMS2\Tools\Exception;

abstract class AbstractController implements ControllerInterface
{
    /** @var array<string, mixed> Data for template */
    public array $templateData = [];

    /** @var Settings|null Project settings */
    public ?Settings $settings = null;

    /** @var Routing|null Routing data */
    public ?Routing $routing = null;

    /** @var string Site Page Title */
    public string $title = '';

    /** @var string Site Page Description */
    public string $description = '';

    /** @var string Site Page Keywords  */
    public string $keyword = '';

    /** @var string Template layout */
    public string $layout = 'default';

    /** @var array<int, string|array<string, string>> JS files to load */
    public array $jsFiles = [];

    /** @var array<int, string|array<string, string>> CSS files to load */
    public array $cssFiles = [];

    /** @var string JS code for Document Ready */
    public string $jsReady = '';

    /** @var string Full template file path for including */
    protected string $_fullTemplatePath = '';

    /** @var string[] Headers for php header() function */
    protected array $_headers = [];

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
     * @throws Exception
     */
    public function renderTemplate(?string $template = null, bool $isFullTemplatePatch = false): void
    {
        if (is_null($template)) {
            $dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
            $template = isset($dbt[1]['function']) ? $dbt[1]['function'] : 'main';
        }

        if ($isFullTemplatePatch) {
            $this->_fullTemplatePath = $template;
        } else {
            $this->_fullTemplatePath = $this->_getFullTemplatePath($template);
        }

        try {
            require($this->_getFullLayoutPath());
        } catch (\Exception $e) {
            throw new Exception('Can\'t render template: ' . $e->getMessage());
        }
    }

    /**
     * Get full layout file path
     *
     * @return string
     */
    protected function _getFullLayoutPath(): string
    {
        return $this->settings->path . $this->_getTemplateName() . DIRECTORY_SEPARATOR . $this->settings->template
            . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->layout . '.php';
    }

    /**
     * Get templates folder name
     *
     * @return string
     */
    protected function _getTemplateName(): string
    {
        if (isset($this->routing->route['useVendor'])) {
            $useVendor = $this->routing->route['useVendor'];
        } else {
            $useVendor = false;
        }

        if ($useVendor) {
            $templatesName = 'templates' . DIRECTORY_SEPARATOR . 'vendor';
        } else {
            $templatesName = 'templates';
        }
        return $templatesName;
    }

    /**
     * Get full template file path
     *
     * @param string $template
     * @return string
     */
    protected function _getFullTemplatePath(string $template): string
    {
        return $this->settings->path . $this->_getTemplateName() . DIRECTORY_SEPARATOR . $this->settings->template
            . DIRECTORY_SEPARATOR . $this->routing->route['controller'] . DIRECTORY_SEPARATOR . $template . '.php';
    }

    /** Echo Template File Body */
    protected function _echoTemplateBody(): void
    {

        if (!include($this->_fullTemplatePath)) {
            throw new Exception('Can\'t echo template body, because can\'t include file "'
                . $this->_fullTemplatePath . '"');
        }
    }

    /** Echo JS script on document ready from $this->jsReady string */
    protected function _echoOnReadyJS(): void
    {
        if (!empty($this->jsReady)) {
            echo PHP_EOL . '<script>' . PHP_EOL
                . "document.addEventListener('DOMContentLoaded', function(){" . PHP_EOL
                . $this->jsReady . "});" . PHP_EOL . '</script>';
        }
    }

    /** Echo JS files (<script ...></script>) from $this->jsFiles array */
    protected function _echoJS(): void
    {
        if (!empty($this->jsFiles)) {
            foreach ($this->jsFiles as $jsFile) {
                echo PHP_EOL;
                if (!is_array($jsFile)) {
                    echo  '<script src="' . $jsFile . '">';
                } else {
                    echo '<script';
                    foreach ($jsFile as $key => $value) {
                        echo ' ' . $key . '="' . $value . '"';
                    }
                    echo '></script>';
                }
            }
        }
    }

    /** Echo CSS files (<link rel="stylesheet" href= ... >) from $this->cssFiles array */
    protected function _echoCSS(): void
    {
        if (!empty($this->cssFiles)) {
            foreach ($this->cssFiles as $cssFile) {
                echo PHP_EOL;
                if (!is_array($cssFile)) {
                    echo  '<link rel="stylesheet" href="' . $cssFile . '"/>';
                } else {
                    echo '<link';
                    foreach ($cssFile as $key => $value) {
                        echo ' ' . $key . '="' . $value . '"';
                    }
                    echo '/>';
                }
            }
        }
    }

    /**
     * Return default Site headers
     *
     * @return string[]
     */
    protected function _getDefaultHeaders(): array
    {
        return [
            'X-Powered-By: newtons',
            'Server: Summit',
            'expires: mon, 26 jul 2000 05:00:00 GMT',
            'cache-control: no-cache, must-revalidate',
            'pragma: no-cache',
            'last-modified: ' . gmdate('d, d m y h:i:s') . ' GMT',
            'X-Frame-Options: SAMEORIGIN',
            'X-XSS-Protection: 1; mode=block;',
            'X-Content-Type-Options: nosniff',
        ];
    }

    /** Echo php heades() from $this->_headers array */
    protected function _echoHeaders(): void
    {
        if (empty($this->_headers)) {
            $this->_headers = $this->_getDefaultHeaders();
        }
        foreach ($this->_headers as $header) {
            header($header);
        }
    }
}