<?php 

require_once __DIR__ . "/../autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$dashboard = new DashboardModel;

$m = new MustacheHelper;

if(!$session_helper->isAdmin() || !$session_helper->isLogined()) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit;
} else {
    $m->setTemplate("admin_article_form")->render([
        'email' => $session_helper->getEmail(),
        'nickname' => $session_helper->getNickname()
    ]);
}
