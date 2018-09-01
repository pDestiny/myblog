<?php

require_once "autoloader.php";

WhoopsHelper::register();

session_start();

$session_helper = new SessionHelper;

$writer = isset($_POST['writer']) ? $_POST['writer'] : null;
$dashboardId = isset($_POST['dashboardId']) ? $_POST['dashboardId'] : null;
$content = isset($_POST['content']) ? $_POST['content'] : null;
$replyTo = isset($_POST['replyTo']) ? $_POST['replyTo'] : null;
$replyToUser = isset($_POST['replyToUser']) ? $_POST['replyToUser'] :null;
$replyLevel = isset($_POST['replyLevel']) ? $_POST['replyLevel'] : null;


if(
    is_null($writer) ||
    is_null($dashboardId) ||
    is_null($content) ||
    is_null($replyTo) ||
    is_null($replyToUser) ||
    is_null($replyLevel)
    ) 
{
    UtilsHelper::showErrorScreen("403", "Invalid Access");
    exit;
} elseif(!$session_helper->isLogined()) {

    UtilsHelper::sendAjaxMsg("info", "You need to login to reply");
    exit;

} elseif(empty($content)) {

    UtilsHelper::sendAjaxMsg("info", "You can't post reply with empty content");

    exit;

} else {

   $reply = new ReplyModel;
   
   $reply->insertReply($dashboardId, $session_helper->getId(), UtilsHelper::h($content), $replyTo , $replyLevel, UtilsHelper::h($replyToUser));

   $returnData = [
       'alertType' => 'success',
       "msg" => '',
       'lastInsertId' => $reply->getLastInsertId()
   ];

   echo json_encode($returnData);

   exit;
}