<?php

require_once "autoloader.php";

session_start();

WhoopsHelper::register();

$session_helper = new SessionHelper;

//post data
    //nickname
    $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : null;
    //email
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    //content
    $content =isset($_POST['content']) ? $_POST['content'] : null;

//validation

//post data null check

if(is_null($nickname) || is_null($email) || is_null($content)) {
    UtilsHelper::showErrorScreen("403", "Forbidden");
    exit;
}

//post data empty check

if(empty($nickname) || empty($email) || empty($content)) {
    UtilsHelper::sendAjaxMsg('info', "Contact me form is not completed");

    exit;
}

//nickname special character check

if($nickname !== filter_var($nickname, FILTER_SANITIZE_SPECIAL_CHARS)) {
    UtilsHelper::sendAjaxMsg("danger", "The nickname has invalid characters");
    exit;
}

//nickname length check
if(mb_strlen($nickname) > 50) {
    UtilsHelper::sendAjaxMsg("warning", "The nickname is strange");
    exit;
}

//nickname duplication check to prevent confusion of

if(!$session_helper->isLogined()) {
    if((new UserModel)->isNicknameExists($nickname)) {
        UtilsHelper::sendAjaxMsg("info", "The nickname already exists. Please use a different name");
        exit;
    }
}


//email validation

//email form check

if(!preg_match("/^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i", $email)) {

    UtilsHelper::sendAjaxMsg('warning', "The email is invalid");
    exit;
}
//email exist check

if(!$session_helper->isLogined()) {

    if((new UserModel)->isEmailExists($email)) {

        UtilsHelper::sendAjaxMsg("info", "The email already exists");
        exit;
    }
}
$content = UtilsHelper::h($content);
//send Email to Admins
$mailer = new MailerHelper(null, "Contact from $nickname!", $content ."<br><br>From : $email");

$mailer->sendMailToAdmin();

UtilsHelper::sendAjaxMsg('success', 'Your Mail has been sent. We will reply ASAP!');
exit;














