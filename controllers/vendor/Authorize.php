<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Authorization Controller Class
 */

namespace app\Controllers\vendor;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Routing\Routing;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class Authorize extends AbstractController implements ControllerInterface
{
    public string $title = 'Authorization';

    public string $redirect = '';

    /**
     * @param Routing|null $routing
     * @param Settings|null $settings
     */
    public function __construct(?Routing $routing, ?Settings $settings)
    {
        parent::__construct($routing, $settings);

        $this->requestParameters->getRequestValue('redirect');
        if (!empty($this->requestParameters->values->redirect)) {
            $this->redirect = $this->requestParameters->values->redirect;
        }
    }

    /**
     * Main method - login form and
     *
     * @return void
     * @throws Exception
     */
    public function main():void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authorization->authorizeRequest();

            if ($this->authorization->getAuthStatus() === true) {
                //TODO redirect to redirect URL or default page
                echo 'yeah';
            } else {
                //TODO echo authorize errors
                echo 'azaza';
            }
        }

        $this->renderTemplate('main');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function registration():void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        $this->renderTemplate('registration');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function resetPassword():void {
        $this->renderTemplate('resetPassword');
    }
}