<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default templates layout file
 *
 * @var AbstractController $this
 */
?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <h1>IceCMS2</h1>
            <p>Fast and Light framework and CMS (under construction)</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h2>Support the project:</h2>
            <ol>
                <li>
                    <h4>Binance:</h4>
                    <p>Scan via the Binance App to send</p>
                    <p><img src="/img/iceCMS2/binance.png" width="250" height="250"></p>
                    <p>My Pay ID: <strong>444136543</strong></p>
                </li>
                <li>
                    <h4>USDT (TRC20)</h4>
                    <p><strong>TFK8xk5BE2YJjuf9mh9jVUchSCayZr9yJa</strong></p>
                </li>
                <li>
                    <h4>USDT (ERC20)</h4>
                    <p><strong>0x7dda48aad71e1319939b30eeda91efa9ea5582de</strong></p>
                </li>
            </ol>
        </div>
    </div>
</div>
