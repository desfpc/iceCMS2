<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract Controller class with token authorization method _authorizationCheck
 */

namespace iceCMS2\Controller;

abstract class AbstractTokenAuthorizationController extends AbstractController implements ControllerInterface
{
    /** @var string Authorization redirect url */
    protected const AUTHORIZE_REDIRECT_URL = '/api/v1/authorize';

    /** @var string Authorization type */
    protected const AUTHORIZE_TYPE = 'token';

    /** Default main method - only render default template
     */
    public function main(): void
    {
        $this->renderJson([
            'message' => 'API is working!',
        ], true);
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
        $data = array_merge($data, [
            'url' => $_SERVER['REQUEST_URI'],
        ]);

        parent::renderJson($data, $success);
    }

    /**
     * Return default Site headers
     *
     * @return string[]
     */
    protected function _getDefaultHeaders(): array
    {

        return array_merge(parent::_getDefaultHeaders(), [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true',
            'Content-Type: application/json; charset=utf-8',
        ]);
    }

    /**
     * Echo headers for redirect to authorization page
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _authorizeRedirect(): void
    {
        $headers = $this->_getDefaultHeaders();
        $headers['HTTP/1.1'] = '401 Unauthorized';

        foreach ($headers as $header) {
            header($header);
        }

        $this->renderJson([
            'message' => 'You need to authorize first',
            'authorize_url' => static::AUTHORIZE_REDIRECT_URL,
        ], false);

        die();
    }
}