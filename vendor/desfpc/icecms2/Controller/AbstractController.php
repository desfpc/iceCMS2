<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract Controller class
 */

namespace iceCMS2\Controller;

use iceCMS2\Authorization\AuthorizationFactory;
use iceCMS2\Authorization\AuthorizationInterface;
use iceCMS2\Routing\Routing;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\FlashVars;
use iceCMS2\Tools\Exception;
use iceCMS2\Tools\RequestParameters;
use JetBrains\PhpStorm\NoReturn;

abstract class AbstractController implements ControllerInterface
{
    /** @var string Authorization redirect url */
    protected const AUTHORIZE_REDIRECT_URL = '/authorize';

    /** @var string Authorization type */
    protected const AUTHORIZE_TYPE = 'session';

    /** @var AuthorizationInterface Authorization object */
    protected AuthorizationInterface $authorization;

    /** @var array Site alerts from FlashVars */
    public array $alerts;

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

    /** @var bool Use vendor layout */
    public bool $vendorLayout = false;

    /** @var RequestParameters|null REQUEST parameters */
    public?RequestParameters $requestParameters = null;

    /** @var array<int, string|array<string, string>> JS files to load */
    public array $jsFiles = [];

    /** @var array<int, string|array<string, string>> CSS files to load */
    public array $cssFiles = [];

    /** @var string JS code for Document Ready */
    public string $jsReady = '';

    /** @var bool If is need to load template file */
    public bool $isTemplate = true;

    /** @var string Full template file path for including */
    protected string $_fullTemplatePath = '';

    /** @var string[] Headers for php header() function */
    protected array $_headers = [];

    /** Class constructor */
    public function __construct(?Routing $routing, ?Settings $settings)
    {
        $this->routing = $routing;
        $this->settings = $settings;
        $this->authorization = AuthorizationFactory::instance($this->settings, static::AUTHORIZE_TYPE);
        //$this->readAlerts();
        $this->requestParameters = new RequestParameters();
    }

    /**
     * Read alerts from FlashVars
     *
     * @return void
     */
    protected function readAlerts(): void
    {
        $flashVars = new FlashVars();
        $this->alerts = [
            'success' => $flashVars->get('success'),
            'error' => $flashVars->get('error'),
            'notice' => $flashVars->get('notice'),
        ];
    }

    /** Default main method - only render default template
     * @throws Exception
     */
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
        $this->readAlerts();

        if (is_null($template)) {
            $dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
            $template = $dbt[1]['function'] ?? 'main';
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
     * Render (print) json data answer
     *
     * @param array $data
     * @param bool $success
     * @return void
     */
    public function renderJson(array $data, bool $success): void
    {
        $this->layout = 'json';
        $this->isTemplate = false;
        require($this->_getFullLayoutPath());

        if (!$success) {
            $this->_headers[] = 'HTTP/1.0 500 Internal Server Error';
            $this->_headers[] = 'Status: 500 Internal Server Error';
        }

        echo json_encode([
            'success' => $success,
            'data' => $data,
        ]);
    }

    /**
     * Get full layout file path
     *
     * @return string
     */
    protected function _getFullLayoutPath(): string
    {
        return $this->_getLayoutPath() . $this->layout . '.php';
    }

    /**
     * Get layout path
     *
     * @return string
     */
    protected function _getLayoutPath(): string
    {
        if ($this->vendorLayout) {
            $layoutTemplateName = $this->_getTemplateName() . DIRECTORY_SEPARATOR . 'vendor';
        } else {
            $layoutTemplateName = $this->_getTemplateName();
        }
        return $this->settings->path . $layoutTemplateName . DIRECTORY_SEPARATOR . $this->settings->template
            . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get templates folder name
     *
     * @return string
     */
    protected function _getTemplateName(): string
    {
        $useVendor = $this->routing->route['useVendor'] ?? false;

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
        return $this->_getTemplatePath() . $this->routing->route['controller'] . DIRECTORY_SEPARATOR . $template . '.php';
    }

    /**
     * Get template files patch
     *
     * @return string
     */
    protected function _getTemplatePath(): string
    {
        return $this->settings->path . $this->_getTemplateName() . DIRECTORY_SEPARATOR . $this->settings->template
            . DIRECTORY_SEPARATOR;
    }

    /** Echo Template File Body
     * @throws Exception
     */
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

    /**
     * Simple redirect
     *
     * @param string $url
     * @param int $code
     * @param bool $noDie
     * @return void
     * @throws Exception
     * @SuppressWarnings(PHPMD)
     */
    protected function _redirect(string $url, int $code = 303, bool $noDie = false): void
    {
        $codeNames = [
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            307 => 'Temporary Redirect',
        ];

        if (!isset($codeNames[$code])) {
            throw new Exception('Invalid redirect code: ' . $code);
        }

        //$headers = $this->_getDefaultHeaders();
        $headers[] = 'Location: ' . $url;
        //$headers[] = 'HTTP/1.1 ' . $code . ' ' . $codeNames[$code];

        foreach ($headers as $header) {
            header($header);
        }

        if ($noDie) {
            die();
        }
    }

    /**
     * Echo headers for redirect to authorization page
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    #[NoReturn] protected function _authorizeRedirect(): void
    {
        $headers = $this->_getDefaultHeaders();
        $headers[] = 'Location: ' . static::AUTHORIZE_REDIRECT_URL . '?redirect=' . urlencode($_SERVER['REQUEST_URI']);
        $headers[] = 'HTTP/1.1 302 Found';

        foreach ($headers as $header) {
            header($header);
        }

        die();
    }

    /**
     * Check authorization for action method (run it in the top of action, that need authorization)
     *
     * @return void
     */
    protected function _authorizationCheck(): void
    {
        if ($this->authorization->getAuthStatus() === false || !$this->authorization->authorizeRequest()) {
            $this->_authorizeRedirect();
        }
    }
}