<?php

require_once "autoloader.php";

WhoopsHelper::register();

$dashboard = new DashboardModel;

$start = isset($_GET['start']) ? $_GET['start'] : null;

if(is_null($start)) {
    UtilsHelper::sendAjaxMsg("danger", "invalid access");
    exit;
} else {
    $end = $start + 5;
    $returnData = [
        'alertType' => "success",
        'previews' => $dashboard->selectArticlesByLimit($start, $end)
    ];

    echo json_encode($returnData);
}