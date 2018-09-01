<?php 

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$returnMsg = [
    'alert' => [
        'alertType' => '',
        'msg' => ''
    ],
    'email' => '',
    'nickname' => ''
];

if(!$session_helper->isLogined()) {
    $returnMsg['alert']['alertType'] = 'danger';
    $returnMsg['alert']['msg'] = 'You cannot access this modal without login.';

    echo json_encode($returnMsg);
    exit;
} else {
    $returnMsg['alert']['alertType'] = 'success';
    $returnMsg['email'] = $session_helper->getEmail();
    $returnMsg['nickname'] = $session_helper->getNickname();

    echo json_encode($returnMsg);
    exit;
}

