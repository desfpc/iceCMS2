<?php
require_once(__DIR__ . '/../vendor/autoload.php');
$f3 = \Base::instance();
$f3->route('GET /f3/test', function() {
    echo json_encode(["status" => "ok"]);
});
$f3->run();
