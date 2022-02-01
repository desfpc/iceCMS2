<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Exeption class
 */

namespace iceCMS2\Tools;

use iceCMS2\Tools\FlashVars;

class Exception extends \Exception
{
    /** @var string FlashVars key for Exeption */
    public const EXEPTION_FLASHVARS_KEY = 'ControllerErrors';
    public const DEBUG_BACKTRACE_FLASHVARS_KEY = 'DebugBacktrace';

    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        $flashVars = new FlashVars();
        $flashVars->set(static::EXEPTION_FLASHVARS_KEY, $message, false);
        $flashVars->set(static::DEBUG_BACKTRACE_FLASHVARS_KEY, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), false);

        parent::__construct($message, $code, $previous);
    }
}