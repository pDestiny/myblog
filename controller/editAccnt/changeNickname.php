<?php 

require_once __DIR__ . '/../autoloader.php';

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$user = new UserModel;

$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : null;

if(!$session_helper->isLogined()) {
    UtilsHelper::sendAjaxMsg('danger', 'Invalid Access. You cannot access this process without login');
    exit;
} elseif(is_null($nickname)) {
    (new MustacheHelper)->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Invalid Access'
    ]);
    exit;
} elseif(preg_match("/\W/", $nickname)) {
    UtilsHelper::sendAjaxMsg("danger", "Invalid Nickname. Nickname must be alphabet or number");
    exit;   
} elseif(mb_strlen($nickname) <= 5 || mb_strlen($nickname) >20) {
    UtilsHelper::sendAjaxMsg("warning", "The number of nickname character is Invalid. The length of nickname must be between 6 - 20");
    exit;
} elseif($user->isNicknameExists($nickname) >=1) {
    if($session_helper->getEmail() !== $user->selectEmail($nickname)) {
        UtilsHelper::sendAjaxMsg("warning", "Nickname already exists");
        exit;
    }else {
        $user->updateNickname($session_helper->getNickname(), $nickname);

        $session_helper->updatePartialSession("nickname", $nickname);

        UtilsHelper::sendAjaxMsg("success", "Nickname has been updated");
        exit;
    }
} else {
    $user->updateNickname($session_helper->getNickname(), $nickname);

    $session_helper->updatePartialSession("nickname", $nickname);

    UtilsHelper::sendAjaxMsg("success", "Nickname has been updated");
    exit;
}