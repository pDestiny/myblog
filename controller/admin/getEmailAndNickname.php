<?php

require_once __DIR__ . "/../autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$user_id = isset($_GET['id']) ? $_GET['id'] : null;

$m = new MustacheHelper;

$returnData = [
    "email" => '',
    "nickname" => '',
    "alertType" => '',
    "msg" => ""
];

if(!$session_helper->isAdmin() || !$session_helper->isLogined() || is_null($user_id)) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => "Forbidden"
    ]);
    exit;
}
else {
    $user = new UserModel;

    $userData = $user->selectEmailAndNickname($user_id);

    if(!$userData) {
        $returnData['alertType'] = 'info';
        $returnData['msg'] = "User Not Found";
        $returnData['email'] = "No Info";
        $returnData['nickname'] = "No Info";
    } else {
        $returnData['alertType'] = 'success';
        $returnData['email'] = $userData['EMAIL'];
        $returnData['nickname'] = $userData['NICKNAME'];
    }

    echo json_encode($returnData);
}