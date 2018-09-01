<?php

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$password1 = isset($_POST['password1']) ? $_POST['password1'] : null;
$password2 = isset($_POST['password2']) ? $_POST['password2'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$pw_recover_cd = isset($_POST['pw_recover_cd']) ? $_POST['pw_recover_cd'] : null;


$user = new UserModel;

if(is_null($password1) || is_null($password2) || is_null($email) || is_null($pw_recover_cd)) {
    
    UtilsHelper::showErrorScreen('403', 'Forbidden');

    exit;
} elseif(empty($password1) || empty($password2)) {
    
    UtilsHelper::sendAjaxMsg('warning', 'Form are not filled completely');
    
    exit;
} elseif($password1 !== $password2) {

    UtilsHelper::sendAjaxMsg('warning', 'Passwords don\'t match');

    exit;

} elseif(mb_strlen($password1) < 8 || mb_strlen($password2) > 30) {

    UtilsHelper::sendAjaxMsg('warning', 'The number of password character must be between 8-30');

    exit;
} elseif($pw_recover_cd !== $user->getPwRecoverCdOf($email)['PW_RECOVER_CD']){
    UtilsHelper::sendAjaxMsg("danger", "Invalid Access. password recover codes don't match");

    exit;
} else {
    //pw_recover_cd를 제거한다.

    $user->deletePwRecoverCd($email);

    //새로 생성한 비밀번호를 데이터 베이스에 업데이트 한다.

    $user->updatePassword($password1, $email);

    //성공 메시지 호출

    UtilsHelper::sendAjaxMsg('success', 'Your new password successfully is updated');
    exit;
}