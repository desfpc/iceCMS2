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
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <h1><?= $this->title ?></h1>
            <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
            <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

            <div id="app">
                <Atable apipath="/api/v1/admin/files"></Atable>
            </div>

            <script type="module" src="/js/vendor/admin/files/files-app.js"></script>
        </div>
    </div>
</div>