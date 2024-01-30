<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Caches main template file
 *
 * @let AbstractController $this
 */
?>
<div class="container">
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

            <?= $this->getLogNameFiles() ?>
            
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
                let fileName = this.getAttribute("data-file");
                loadLogs(fileName);
            });
        });

        function loadLogs(fileName) {
            let logsContainer = document.getElementById("logs-container");
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "/api/v1/get-logs/" + fileName, true);
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