<?php

    require_once "autoloader.php";

    session_start();

    $renderData =[
        'siteHeading' => '',
        'subHeading' => '',
        'showMode' => true,
        'nickname' => '',
        'written' => '',
        'isLogined' => false,
        'htmlContent' => '',
        'img' => '/view/img/post-bg.jpg'
    ];

    $dashboard = new DashboardModel;

    $session_helper = new SessionHelper;

    $m = new MustacheHelper;

    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if($session_helper->isLogined()) {
        $renderData['isLogined'] = true;
    }

    if(is_null($id)) {
        UtilsHelper::showErrorScreen("403", "Invalid Access");
        exit;
    } else {
        $user = new UserModel;

        $article = $dashboard->selectArticle($id); 

        $renderData['siteHeading'] = $article['TITLE'];
        $renderData['subHeading'] = $article['SUBTITLE'];
        $renderData['nickname'] = $user->selectNicknameById($article['NICKNAME']);
        $renderData['written'] = (new DateTime($article['WRITTEN']))->format('Y-m-d');
        $renderData['htmlContent'] = $article['HTML_CONTENT'];
        $renderData['id'] = $id;

        $m->setTemplate("show")->render($renderData);
    }