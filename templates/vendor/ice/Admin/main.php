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
            <p>&nbsp;</p>
            <h1>Admin dashboard</h1>

            <hr>
            <h2>IceCMS2</h2>
            <p>
                Lite and fast PHP framework + CMS
            </p>
            <table class="table table-hover table-dark">
                <tbody><tr>
                    <td>Name</td>
                    <th>iceCMS 2</th>
                </tr><tr>
                    <td>URL</td>
                    <th><a href="https://github.com/desfpc/iceCMS2" target="_blank">github.com/desfpc/iceCMS2</a></th>
                </tr><tr>
                    <td>Version</td>
                    <th>0.1</th>
                </tr><tr>
                    <td>Developer</td>
                    <th><a href="https://github.com/desfpc" target="_blank">Sergey Peshalov</a></th>
                </tr><tr>
                    <td>Programming language</td>
                    <th>PHP 8.1</th>
                </tr><tr>
                    <td>Data Bases</td>
                    <th>MySql 8, Redis</th>
                </tr></tbody></table>
            <p>Technology stack: PHP 8.1, MySQL 8, Redis, Vue.js 3, Bootstrap 5</p>
            <p>The main principles of iceCMS2 are simplicity and speed. Therefore, there are not many "standard"
                abstractions and principles, such as ORM and 100% SOLID adherence. Direct SQL queries and fast native
                database drivers! No abstractions for the sake of abstractions!</p>
        </div>
    </div>
</div>
