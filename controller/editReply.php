<?php

    require_once "autoloader.php";

    WhoopsHelper::register();

    $reply_id = isset($_POST['replyId']) ? $_POST['replyId'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;

    session_start();

    $session_helper = new SessionHelper;

    $reply = new ReplyModel;

    if(!$session_helper->isLogined()) {
        UtilsHelper::sendAjaxMsg("dnager", 'You are not allowed to edit this reply without login');
        exit;   
    } elseif(is_null($reply_id) || is_null($content)) {
        UtilsHelper::showErrorScreen("403", "Forbidden");
    } elseif(!$session_helper->isAdmin()) {
        if($reply->getReplyUserId($reply_id) === $session_helper->getId()) {
            $reply->updateContent($reply_id, $content);

            UtilsHelper::sendAjaxMsg("success", 'The reply has been updated');
            exit;   

        } else {
            UtilsHelper::sendAjaxMsg("warning", 'The reply writer only can edit the reply');
            exit;   
        }
    } elseif($session_helper->isAdmin()) {
        $reply->updateContent($reply_id, $content);

        UtilsHelper::sendAjaxMsg("success", 'The reply has been updated');
        exit;   
    }
