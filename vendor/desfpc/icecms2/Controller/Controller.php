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
     * @param ?string $template
     */
    public function renderTemplate(?string $template = null): void
    {
        if (is_null($template)) {
            $dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
            $template = isset($dbt[1]['function']) ? $dbt[1]['function'] : 'main';
        }
        Visualijoper::visualijop($template);
    }
}