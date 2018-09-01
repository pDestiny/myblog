<?php

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$reply = new ReplyModel;

$reply_id = isset($_POST['replyId']) ? $_POST['replyId'] : null;

if(!$session_helper->isLogined()) {
    UtilsHelper::sendAjaxMsg("danger", 'You are not allowed to delete this reply without login');
    exit;
} elseif(!$session_helper->isAdmin()) {
    if($reply->getReplyUserId($reply_id) === $session_helper->getId()) {
        $reply->deleteReply($reply_id);
        
        UtilsHelper::sendAjaxMsg("success", "The reply has been deleted");

        exit;
    } else {
        UtilsHelper::sendAjaxMsg("warning", "You are not allowed to delete this reply");
        exit;
    }
} elseif($session_helper->isAdmin()) {
    $reply->deleteReply($reply_id);

    UtilsHelper::sendAjaxMsg("success", "The reply has been deleted");
    exit;
}