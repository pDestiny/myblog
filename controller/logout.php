<?php

    require_once "autoloader.php";

    WhoopsHelper::register();

    session_start();

    $session_helper = new SessionHelper;

    $returnData = [
        'alertType' => '',
        'msg' => ''
    ];

    if(!$session_helper->isLogined()) {

        $returnData['alertType'] = "danger";
        $returnData['msg'] = "Invalid Access";

        echo json_encode($returnData);
        exit;
        
    } else {
        $session_helper->destroySession();
        $returnData['alertType'] = "success";
        $returnData['msg'] = "You Just Logout. Have a Nice Day!";

        echo json_encode($returnData);
        exit;
    }

