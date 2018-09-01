<?php

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$email = isset($_GET['email']) ? $_GET['email'] : null;
$pw_recover_cd = isset($_GET['pw_recover_cd']) ? $_GET['pw_recover_cd'] : null;

$m = new MustacheHelper;

$session_helper = new SessionHelper;

if($session_helper->isLogined()) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit;
}

if(is_null($email) || is_null($pw_recover_cd)) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit;
} elseif(empty($email) || empty($pw_recover_cd)) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    exit; 
} else{
    $user = new UserModel;

    if($pw_recover_cd === $user->getPwRecoverCdOf($email)['PW_RECOVER_CD']) {
        //pw_recover_cd와 데이터 베이스의 해당 이메일과 일치하는 PW_RECOVER_CD가 있다면

        $m->setTemplate("reset_password")->render([
           'email' => $email,
           'pw_recover_cd' => $pw_recover_cd,
           "img" => '/view/img/home-bg.jpg'
        ]);

        exit;
    } else {
        $m->setTemplate('error')->render([
            'errorCode' => '403',
            'errorMsg' => 'Forbidden'
        ]);
        exit; 
    }
} 
// 해당 이메일에 존재하는 pw_recover_cd가 일치하는지 확인한다.

/* 비밀번호 교체 로직

    비밀번호를 교체하여 저장한 다음 교체한 비밀번호를 메일로 보내준다.

    4. 사용자가 자신의 메일로 가서 헤쉬된 코드와 이메일이 적힌 링크를 클릭한다.

    5. 사용자가 자신의 이메일에서 링크를 클릭하였으니 본인인 것이 확인 되었다고 판단한다.

    6. resetPassword 에서 이메일과 PW_RECOVER_CD가 일치할 경우에 비밀번호를 변경할 수 있는 폼을 보여준다.

    7. 사용자가 폼을 작성하고 제
    출하면 원래 있던 이메일의 PW_RECOVER_CD는 삭제하고 새로 생성된 패스워드를 입력한다.

    8. 위의 모든 준비가 끝났을 경우에 home.php 로 redirection 한다. */
