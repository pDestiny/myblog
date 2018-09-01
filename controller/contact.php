<?php 

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$m = new MustacheHelper;

$renderData = [
    'isLogined' => $session_helper->isLogined(),
    'nickname' => $session_helper->getNickname(),
    'email' => $session_helper->getEmail(),
    "siteHeading" => "Contact Me!",
    "subHeading" => '',
    "showMode" => false,
    'img' => '/view/img/contact-bg.jpg'
];

if($session_helper->isLogined()) {
    
    $m->setTemplate("contact")->render($renderData);
}
else {
    $renderData['nickname'] = '';
    $renderData['email'] = '';

    $m->setTemplate("contact")->render($renderData);
}











