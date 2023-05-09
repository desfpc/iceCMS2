<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;
use iceCMS2\Models\User;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Authorization template
 *
 * @var AbstractController $this
 *
 */

/** @var User $user */
$user = $this->templateData['user'];

?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <p>&nbsp;</p>
            <h1><?= $user->get('email'); ?> profile</h1>

            <p>&nbsp;</p>
        </div>
    </div>
</div>
