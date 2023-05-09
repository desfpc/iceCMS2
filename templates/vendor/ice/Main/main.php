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
            <h4>Fast and Light framework and CMS (under construction)</h4>
        </div>
    </div>
</div>
