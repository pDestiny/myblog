<?php

require_once __DIR__ . "/../controller/autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper();

if($session_helper->isLogined()) {
    echo json_ecnode([
        'alertType' => 'danger',
        'msg' => "You CANNOT Register in Logined status"
    ]);
    exit;
}

$requestedData = [
    "email" => isset($_POST['email']) ? $_POST['email'] : null,
    "nickname" => isset($_POST['nickname']) ? $_POST['nickname'] : null,
    "password1" => isset($_POST['password1']) ? $_POST['password1'] : null,
    "password2" => isset($_POST['password2']) ? $_POST['password2'] : null
];

$returnMsg = [
    "alertType" => '',
    "msg" => '',
];

if(is_null($requestedData['email']) || is_null($requestedData['nickname']) || is_null($requestedData['password1']) || is_null($requestedData['password2'])){
    (new MustacheHelper)->setTemplate("error")->render([
        "errorCode" => '403',
        "errorMsg" => 'Forbidden',
    ]);
    exit;
}

//form을 입력하지 않았을 경우;

if(
    empty($requestedData['email']) ||
    empty($requestedData['nickname']) ||
    empty($requestedData['password1']) ||
    empty($requestedData['password2'])
) {

    $returnMsg['alertType'] = "info";
    $returnMsg['msg'] = "Form was not filled completely.";

    echo json_encode($returnMsg);
//이메일이 정규식에 맞는지 확인
} elseif(!preg_match("/^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i", $requestedData['email'])) {
    $returnMsg['alertType'] = "danger";
    $returnMsg['msg'] = "Invalid Email";

    echo json_encode($returnMsg);
//nickname이 정규식에 맞는지 확인
} elseif(preg_match("/\W/", $requestedData['nickname'])) {
    $returnMsg['alertType'] = "danger";
    $returnMsg['msg'] = "Invalid Nickname. Nickname must be alphabet or number";

    echo json_encode($returnMsg);
}
//nickname 길이 확인
elseif((mb_strlen($requestedData['nickname']) <=5 || mb_strlen($requestedData['nickname']) >20)) {
    $returnMsg['alertType'] = "danger";
    $returnMsg['msg'] = "The number of nickname character is Invalid. The length of nickname must be between 6 - 20";

    echo json_encode($returnMsg);
//비밀번호 숫자 확인
} elseif($requestedData['password1'] != $requestedData['password2']) {
    $returnMsg['alertType'] = "warning";
    $returnMsg['msg'] = "password doesn't match. check you password again";

    echo json_encode($returnMsg);
//비밀번호 길이 확인
} elseif( mb_strlen($requestedData['password1']) < 8 || mb_strlen($requestedData['password1']) > 30) {
    $returnMsg['alertType'] = "warning";
    $returnMsg['msg'] = "The number of password character must be between 8 - 30";

    echo json_encode($returnMsg);

} else {
    //이메일 중복확인
    $user = new UserModel();

    if($user->isEmailExists($requestedData['email']) >= 1) {
        $returnMsg['alertType'] = "warning";
        $returnMsg['msg'] = "The email already exits";

        echo json_encode($returnMsg);
        exit;
    //nickname 중복확인
    } elseif($user->isNicknameExists($requestedData['nickname']) >= 1) {
        $returnMsg['alertType'] = "warning";
        $returnMsg['msg'] = "The nickname already exits";

        echo json_encode($returnMsg);
        exit;
    //이메일도 중복이 아니고 닉네임도 중복이 아니라면 아이디를 등록하고 저장한다.
    } else {
        //데이터를 세니타이징 하고 저장
        $email = UtilsHelper::h($requestedData['email']);
        $nickname = UtilsHelper::h($requestedData['nickname']);
        $password = password_hash($requestedData['password1'], PASSWORD_BCRYPT);
        
        $id = $user->saveUser($email,$nickname, $password);

        //세션에 추가
        $session_helper->setSession(
            [
                "id" => $id,
                "email" => $email,
                "nickname" => $nickname,
                "position" => "USER"
            ]
        );
        
        //success 메시지를 전달한다.
        $returnMsg['alertType'] = 'success';
        $returnMsg['msg'] = 'Login success';

        echo json_encode($returnMsg);
        exit;
    }
}






