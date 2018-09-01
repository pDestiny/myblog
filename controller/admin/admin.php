<?php 

require_once __DIR__ . '/../autoloader.php';

WhoopsHelper::register();

session_start();

WhoopsHelper::register();

$session_helper = new SessionHelper;

$m = new MustacheHelper;

$dashboard = new DashboardModel;

if(!$session_helper->isAdmin() || !$session_helper->isLogined()) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit;
} else {
    $articles = $dashboard->selectArticles();

    $m->setTemplate('admin')->render([
        "dashboard-items" => $articles,
        'email' => $session_helper->getEmail(),
        'nickname' => $session_helper->getNickname()
    ]);
}



