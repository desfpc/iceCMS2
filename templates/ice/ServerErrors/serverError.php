<?php
declare(strict_types=1);

use iceCMS2\Controller\Controller;
use iceCMS2\Tools\Exception;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default templates layout file
 *
 * @var Controller $this
 */
?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="mt-5 alert alert-danger" role="alert">
                <h1 class="display-1">500 Internal Server Error</h1>
                <hr>
                <p><?= $this->settings->dev === true ? $this->templateData[Exception::EXEPTION_FLASHVARS_KEY] : '' ?></p>
            </div>
            <?php
            if ($this->settings->dev === true && !empty($this->templateData[Exception::DEBUG_BACKTRACE_FLASHVARS_KEY])) {
                echo '<div class="mt-2 alert alert-warning">';
                echo '<hr />';
                foreach ($this->templateData[Exception::DEBUG_BACKTRACE_FLASHVARS_KEY] as $key => $trace) {
                    echo '<strong>' . $key . ': </strong>';
                    echo '<p>File: ' . $trace['file'] . PHP_EOL;
                    echo '<br>Line: ' . $trace['line'] . PHP_EOL;
                    if (!empty($trace['function'])) {
                        echo '<br>Function: ' . $trace['function'] . PHP_EOL;
                    }
                    if (!empty($trace['class'])) {
                        echo '<br>Class: ' . $trace['class'] . PHP_EOL;
                    }
                    echo '</p><hr />';
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

