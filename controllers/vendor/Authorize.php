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

class Authorize extends AbstractController implements ControllerInterface
{
    public string $title = 'Authorization';

    public string $redirect = '';

    public function __construct(?Routing $routing, ?Settings $settings)
    {
        parent::__construct($routing, $settings);

        $this->requestParameters->getRequestValue('redirect');
        if (!empty($this->requestParameters->values->redirect)) {
            $this->redirect = $this->requestParameters->values->redirect;
        }
    }
}