<?php

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$user = new UserModel;

$session_helper = new SessionHelper;

$email = isset($_POST['email']) ? $_POST['email'] : null;

if($session_helper->isLogined()) {
    UtilsHelper::showErrorScreen("403", 'Forbidden');
    exit;
} elseif(is_null($email)) {
    UtilsHelper::showErrorScreen("403", 'Forbidden');
    exit;
} elseif(empty($email)) {
    UtilsHelper::showErrorScreen("403", 'Forbidden');
    exit;
} elseif ($user->isEmailExists($email) == 0) {
    UtilsHelper::showErrorScreen("403", 'Forbidden');
    exit;
} else {

    $user->deletePwRecoverCd($email);

    UtilsHelper::sendAjaxMsg("info", "Password find process canceled. All data related are deleted");
    
    
}