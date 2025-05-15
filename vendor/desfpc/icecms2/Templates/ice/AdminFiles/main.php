<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Admin Files main template file
 *
 * @let AbstractController $this
 */
?>
<div class="container-fluid">
    <?php include $this->settings->path . '/templates/ice/layouts/_breadCrumbs.php'; ?>
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <h1><?= $this->title ?></h1>
            <?php include('_imageSizes.php'); ?>

            <div id="app">
                <Atable apipath="/api/v1/admin/files"></Atable>
            </div>

            <script type="module" src="/js/vendor/admin/files/files-app.js"></script>
        </div>
    </div>
</div>