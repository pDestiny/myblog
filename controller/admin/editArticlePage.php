<?php

require_once __DIR__ . "/../autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$dashboard = new DashboardModel;

$m = new MustacheHelper;

$renderData = [
    'editMode' => true,
    'title' => '',
    'subtitle' => '',
    'mdContent' => '',
    'parsedContent' => '',
    'id' => ''
];

$id = isset($_GET['dashboard_id']) ? $_GET['dashboard_id'] : null;

if(!$session_helper->isAdmin() || !$session_helper->isLogined() || is_null($id)) {

    UtilsHelper::showErrorScreen("403", "Forbidden");

    exit;
} else {
    
    $data = $dashboard->selectArticle($id);

    
    $renderData['id'] = $id;
    $renderData['title'] = $data['TITLE'];
    $renderData['subtitle'] = $data['SUBTITLE'];
    $renderData['mdContent'] = $data['MD_CONTENT'];
    $renderData['parsedContent'] = $data['HTML_CONTENT'];
    $renderData['email'] = $session_helper->getEmail();
    $renderData['nickname'] = $session_helper->getNickname();

    $m->setTemplate("admin_article_form")->render($renderData);

    exit;
}

