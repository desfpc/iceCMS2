<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Admin logs template file
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

            <p>
                <a href="/admin/logs/clear-all-logs/"
                   class="btn btn-danger"
                   title="Clear all logs">
                Clear all logs
                </a>
                <a href="/admin/logs/clear-period-logs/"
                   class="btn btn-danger"
                   title="the period is set in the project settings in the settings folder">
                Clear the logs for the period
                </a>
            </p>

            <?= $this->instance() ?>
            
            <div class="bg-dark text-white">
                <p id="logs-container"></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let logs = document.querySelectorAll(".log");
        logs.forEach(function (log) {
            log.addEventListener("click", function () {
                const fileName = this.getAttribute("data-file");
                const alias = this.getAttribute('data-alias');
                const createdTime = this.getAttribute('data-created_time');

                loadLogs(fileName, alias, createdTime);
            });
        });

        function loadLogs(fileName, alias, createdTime) {
            let logsContainer = document.getElementById("logs-container");
            let xhr = new XMLHttpRequest();
            if(null !== fileName) {
                xhr.open("GET", "/api/v1/admin/get-logs/" + fileName, true);
            } else {
                xhr.open("GET", "/api/v1/admin/get-db-logs/" + alias + '_' + createdTime, true);
            }
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        logsContainer.innerHTML = "";
                        logsContainer.setAttribute("class", "p-3 mb-2");
                        response.data.forEach(function (log) {
                            logsContainer.innerHTML += log;
                        });
                    } else {
                        console.error("Failed to load logs.");
                    }
                } else {
                    console.error("Failed to load logs.");
                }
            };
            xhr.onerror = function () {
                console.error("Network error occurred.");
            };
            xhr.send();
        }
    });
</script>