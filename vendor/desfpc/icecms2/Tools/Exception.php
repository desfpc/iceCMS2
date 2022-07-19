<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Exception class
 */

namespace iceCMS2\Tools;

use Throwable;

class Exception extends \Exception
{
    /** @var string FlashVars key for Exception */
    public const EXEPTION_FLASHVARS_KEY = 'ControllerErrors';
    /** @var string FlashVars key for backtrace */
    public const DEBUG_BACKTRACE_FLASHVARS_KEY = 'DebugBacktrace';

    /**
     * Exception constructor
     *
     * @param string $message
     * @param int $code Exception code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null) {
        $flashVars = new FlashVars();
        $flashVars->set(static::EXEPTION_FLASHVARS_KEY, $message, false);
        $flashVars->set(static::DEBUG_BACKTRACE_FLASHVARS_KEY, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), false);

        parent::__construct($message, $code, $previous);
    }
}