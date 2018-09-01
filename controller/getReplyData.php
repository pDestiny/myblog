<?php

require_once "autoloader.php";

WhoopsHelper::register();

$reply = new ReplyModel;

$dashboard_id = isset($_GET['dashboardId']) ? $_GET['dashboardId'] : null;

if(is_null($dashboard_id)) {
    UtilsHelper::showErrorScreen("403", "Forbidden");
    exit;
} else {

    echo json_encode($reply->getReplise($dashboard_id));
    exit;
}