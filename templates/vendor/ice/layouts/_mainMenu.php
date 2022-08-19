<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * MainMenu template file
 *
 * @var AbstractController $this
 */

use iceCMS2\Controller\AbstractController;

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/img/iceCMS2/logofw.svg" alt="" width="40" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'Main'){
                        echo 'active';
                    } ?>" aria-current="page" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'HelloWorld'){
                        echo 'active';
                    } ?>" href="/hello-world/">HelloWorld</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'Admin'){
                        echo 'active';
                    } ?>" href="/admin/">Admin</a>
                </li>
            </ul>
        </div>
    </div>
</nav>