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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark admin-menu">
    <div class="container">
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'Admin'
                        && $this->routing->route['controllerMethod'] === 'main') {
                        echo 'active';
                    } ?>" aria-current="page" href="/admin/"><i class="bi-speedometer" style="font-size: 20px; color: #408fab;"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'AdminMaterials') {
                        echo 'active';
                    } ?>" aria-current="page" href="/admin/materials/"><i class="bi-database" style="font-size: 20px; color: #408fab;"></i> Materials</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'AdminFiles') {
                        echo 'active';
                    } ?>" aria-current="page" href="/admin/files/"><i class="bi-files" style="font-size: 20px; color: #408fab;"></i> Files</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'AdminUsers') {
                        echo 'active';
                    } ?>" aria-current="page" href="/admin/users/"><i class="bi-people" style="font-size: 20px; color: #408fab;"></i> Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($this->routing->route['controller'] === 'AdminSettings') {
                        echo 'active';
                    } ?>" aria-current="page" href="/admin/settings/"><i class="bi-gear" style="font-size: 20px; color: #408fab;"></i> Settings</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
