<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Controller Interface
 */

namespace iceCMS2\Controller;

interface ControllerInterface
{
    /** Default main method - only render default template */
    public function main(): void;

    /**
     * Render (print) template
     *
     * @param ?string $template
     */
    public function renderTemplate(?string $template): void;
}