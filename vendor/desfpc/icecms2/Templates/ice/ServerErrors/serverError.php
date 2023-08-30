<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;
use iceCMS2\Tools\Exception;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default templates layout file
 *
 * @var AbstractController $this
 */
$errors = '';
$trace = '';
if ($this->settings->dev === true) {
    if (!empty($this->templateData[Exception::EXEPTION_FLASHVARS_KEY])) {
        if (!is_array($this->templateData[Exception::EXEPTION_FLASHVARS_KEY])) {
            $errors = '<p>' . $this->templateData[Exception::EXEPTION_FLASHVARS_KEY] . '</p>';
        } else {
            foreach ($this->templateData[Exception::EXEPTION_FLASHVARS_KEY] as $key => $value) {
                $errors .= '<p>' . ($key + 1) . '. ' . $value . '</p>';
            }
        }
    }
    if (!empty(!empty($this->templateData[Exception::DEBUG_BACKTRACE_FLASHVARS_KEY]))) {
        $trace .= '<div class="mt-2 alert alert-warning">';
        $trace .= '<hr />';
        foreach ($this->templateData[Exception::DEBUG_BACKTRACE_FLASHVARS_KEY] as $key => $val) {
            $trace .= '<strong>' . $key . ': </strong>';
            $trace .= '<p>File: ' . $val['file'] . PHP_EOL;
            $trace .= '<br>Line: ' . $val['line'] . PHP_EOL;
            if (!empty($val['function'])) {
                $trace .= '<br>Function: ' . $val['function'] . PHP_EOL;
            }
            if (!empty($val['class'])) {
                $trace .= '<br>Class: ' . $val['class'] . PHP_EOL;
            }
            $trace .= '</p><hr />';
        }
        $trace .= '</div>';
    }
}

?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="mt-5 alert alert-danger" role="alert">
                <h1 class="display-1">500 Internal Server Error</h1>
                <hr>
                <?= $errors ?>
            </div>
            <?= $trace ?>
        </div>
    </div>
</div>

