<?php

require_once __DIR__ . '/../autoloader.php';

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$parser = new MarkParserHelper;

$dashboard = new DashboardModel;

$m = new MustacheHelper;

$title = isset($_POST['title']) ? $_POST['title'] : null;
$subtitle = isset($_POST['subtitle']) ? $_POST['subtitle'] : null;
$content = isset($_POST['content']) ? $_POST['content'] : null;

if(!$session_helper->isAdmin() || !$session_helper->isLogined()) {
    $m->setTemplate('error')->render([
        "errorCode" => '403',
        "errorMsg" => 'Forbidden'
    ]);
    exit;
}

if(is_null($title) || is_null($subtitle) || is_null($content)) {
    $m->setTemplate('error')->render([
        "errorCode" => '403',
        "errorMsg" => 'Forbidden'
    ]);
    exit;
}

if(empty($title) || empty($subtitle) || empty($content)) {
    UtilsHelper::sendAjaxMsg('info', "Form not completed");
    exit;
} else {
    $parser->setOriginText($content);
    $parser->setParsedText();

    $dashboard->saveArticle($title, $subtitle, $session_helper->getEmail(), $parser->getOriginText(), $parser->getParsedText());
    UtilsHelper::sendAjaxMsg("success", "The article has been successfully posted");
    exit;

}






