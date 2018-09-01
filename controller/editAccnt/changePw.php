<?php 

require_once __DIR__ .'/../autoloader.php';

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$user = new UserModel;

$m = new MustacheHelper;

$password1 = isset($_POST['password1']) ? $_POST['password1'] : null;
$password2 = isset($_POST['password2']) ? $_POST['password2'] : null;


if(!$session_helper->isLogined()) {
    UtilsHelper::sendAjaxMsg("danger", "Not allowed to access without login");
    exit;
} elseif(is_null($password1) || is_null($password2)) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit;
}else {
    $returnMsg =[
        'alertType' => 'success',
        'email' => $session_helper->getEmail(),
        'pw_recover_cd' => $user->geneartePwRecoverCd()->savePwRecoverCd($session_helper->getEmail())
    ];
    echo json_encode($returnMsg);
    exit;
}