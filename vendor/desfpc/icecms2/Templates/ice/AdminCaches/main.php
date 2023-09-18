<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User main template file
 *
 * @var AbstractController $this
 */
?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <h1><?= $this->title ?></h1>
            <p>
                <a href="/admin/caches/clear/" class="btn btn-danger">Clear all caches</a>
                <a href="/admin/caches/clear-php/" class="btn btn-danger">Clear PHP caches</a>
            </p>
        </div>
    </div>
</div>
