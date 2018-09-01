<?php 

require_once __DIR__ . "/../autoloader.php";

WhoopsHelper::register();

session_start();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

$session_helper = new SessionHelper;

if(!$session_helper->isAdmin() || !$session_helper->isLogined() || is_null($id) || is_null($type) || !preg_match("/(dashboard|user)/", $type)) {
    UtilsHelper::showErrorScreen("403", 'Forbidden');
    exit;
}

if($type === 'dashboard') {
    $dashboard = new DashboardModel;

    $dashboard->deleteArticle($id);
    
    UtilsHelper::sendAjaxMsg("success", '');
    exit;

} elseif($type === 'user') {
    $user = new UserModel;
    
    $user->deleteUser($id);

    UtilsHelper::sendAjaxMsg("success", '');
    exit;
    
} else {
    UtilsHelper::showErrorScreen("403", 'Forbidden');
    exit;
}
