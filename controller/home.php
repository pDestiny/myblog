<?php 
    require_once "autoloader.php";
    
    session_start();

    WhoopsHelper::register();

    $session_helper = new SessionHelper;
    // var_dump($session_helper->getId());
    // var_dump($session_helper->getEmail());
    // var_dump($session_helper->getNickname());
    // var_dump($session_helper->getPosition());
    $dashboard = new DashboardModel;

    $data = $dashboard->selectArticlesByLimit(0, 5);

    array_walk($data, function(&$item) {
        $item['WRITTEN'] = (new DateTime($item['WRITTEN']))->format('Y-m-d');
    });

    $data = [
        "isLogined" => $session_helper->isLogined(),
        "siteHeading" => "SJ's Agavond Blog",
        "subHeading" => "No pain No gain",
        "dashboard-items" => $data,
        "img" => "/view/img/home-bg.jpg"
    ];

    (new MustacheHelper)->setTemplate('index')->render($data);
?>