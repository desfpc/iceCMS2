<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Registration template
 *
 * @var AbstractController $this
 */
?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <p>&nbsp;</p>
            <h1>Registration</h1>
            <div class="col-6">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label required">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                               value="<?= $this->requestParameters->values->email ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               value="<?= $this->requestParameters->values->password ?>">
                    </div>
                    <div class="mb-3">
                        <label for="rePassword" class="form-label required">Re-enter password</label>
                        <input type="password" class="form-control" id="rePassword" name="rePassword"
                               value="<?= $this->requestParameters->values->rePassword ?>">
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary" value="Sign up">
                    </div>
                    <p><a href="/authorize">Login</a> | <a href="/reset-password">Reset password</a></p>
                </form>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>
</div>
