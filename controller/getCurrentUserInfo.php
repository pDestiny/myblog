<?php

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$returnData = [
    'alertType' => '',
    'email' => '',
    'nickname' => '',
    'user_id' => '',
];

if($session_helper->isLogined()) {
    $returnData['email'] = $session_helper->getEmail();
    $returnData['nickname'] = $session_helper->getNickname();
    $returnData['user_id'] = $session_helper->getId();
    $returnData['alertType'] = 'success';
} else {
    $returnData['alertType'] = '';
}

echo json_encode($returnData);