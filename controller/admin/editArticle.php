<?php

    require_once __DIR__ . "/../autoloader.php";

    WhoopsHelper::register();

    session_start();

    $session_helper = new SessionHelper;

    $dashboard = new DashboardModel;
    $id = isset($_POST['id']) ? $_POST['id'] :null;
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $subtitle = isset($_POST['subtitle']) ? $_POST['subtitle'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;

    if(
        !$session_helper->isAdmin() || 
        !$session_helper->isLogined() ||
        is_null($title) || is_null($subtitle) || is_null($content) || is_null($id)
    ) {
        UtilsHelper::showErrorScreen("403", "Forbidden");

        exit;

    } elseif(empty($title) || empty($subtitle) || empty($content)){
        UtilsHelper::sendAjaxMsg("info", "Forms are not completed");
        exit;
    } else {
        $parser = new MarkParserHelper;
        $parser->setOriginText($content);
        $parser->setParsedText();

        $dashboard->updateArticle($id, $title, $subtitle, $parser->getOriginText(), $parser->getParsedText());
        UtilsHelper::sendAjaxMsg("success", "Article has been updated");
        exit;
    }