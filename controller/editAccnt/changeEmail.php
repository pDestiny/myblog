<?php

require_once __DIR__ . '/../autoloader.php';

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$user = new UserModel;

$email = isset($_POST['email']) ? $_POST['email'] : null;

if(!$session_helper->isLogined()) {
    UtilsHelper::sendAjaxMsg('danger', 'Invalid Access You cannot access this process without login');
    exit;
} elseif(is_null($email)) {
    (new MustacheHelper)->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit;
} elseif(!preg_match("/^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i", $email)){
    UtilsHelper::sendAjaxMsg("danger", "Invalid Email Format");
    exit;
} elseif($user->isEmailExists($email) >= 1) {
    //현재와 동일한 이메일로 바꾸려고 하는지 확인한다.
    if($session_helper->getNickname() !== $user->selectNickname($email)) {
        UtilsHelper::sendAjaxMsg('warning', "Email already exists");
        exit;
    } else {
        $user->updateEmail($session_helper->getEmail(), $email);

        UtilsHelper::sendAjaxMsg("success", 'Email has been updated');
        
        $session_helper->setPartialSesion('email', $email);
    }
} else {
    $user->updateEmail($session_helper->getEmail(), $email);

    UtilsHelper::sendAjaxMsg("success", 'Email has been updated');
    
    $session_helper->updatePartialSession('email', $email);
    exit;
}

