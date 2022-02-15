<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Not Found Error Class
 */

namespace app\Controllers\vendor;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Tools\Exception;
use iceCMS2\Tools\FlashVars;

class ServerErrors extends AbstractController implements ControllerInterface
{
    /**
     * 505 Server Error page
     *
     * @throws Exception
     */
    public function serverError()
    {
        $this->templateData[Exception::EXEPTION_FLASHVARS_KEY] = (new FlashVars())->get(Exception::EXEPTION_FLASHVARS_KEY);
        $this->templateData[Exception::DEBUG_BACKTRACE_FLASHVARS_KEY] = (new FlashVars())->get(Exception::DEBUG_BACKTRACE_FLASHVARS_KEY);
        $this->_headers = $this->_getDefaultHeaders();
        $this->_headers[] = 'HTTP/1.0 500 Internal Server Error';
        $this->_headers[] = 'Status: 500 Internal Server Error';
        $this->renderTemplate('serverError');
    }

    /**
     * 404 Not Found Page
     *
     * @throws Exception
     */
    public function notFound()
    {
        $this->_headers = $this->_getDefaultHeaders();
        $this->_headers[] = 'HTTP/1.0 404 Not Found';
        $this->_headers[] = 'Status: 404 Not Found';
        $this->renderTemplate('notFound');
    }
}