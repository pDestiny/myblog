<?php

    require_once "autoloader.php";

    WhoopsHelper::register();

    session_start();

    $session_helper = new SessionHelper;

    $user = new UserModel;

    $requestedData = [
        "email" => isset($_POST['email']) ? $_POST['email'] : null,
        "password" => isset($_POST['password']) ? $_POST['password'] : null
    ];

    $returnData = [
        'alertType' => '',
        'msg' => ''
    ];

    if($session_helper->isLogined()) {
        $returnData['alertType'] = 'danger';
        $returnData['msg'] = "You CANNOT Login in Logined status";
        
        echo json_encode($returnData);
        exit;
    }

    //1. 폼이 다 체워 졌는지를 확인.
    if(empty($requestedData['email']) || empty($requestedData['password'])) {
        $returnData['alertType'] = 'info';
        $returnData['msg'] = "Form was not entered completely.";
        
        echo json_encode($returnData);
        exit;
    } 
    //2. 이메일이 존재하는지 확인
    elseif($user->isEmailExists($requestedData['email']) == 0) {
        $returnData['alertType'] = 'warning';
        $returnData['msg'] = "Your email doesn't exist!";
        
        echo json_encode($returnData);
        exit;
    }
    //3. 이메일에 해당하는 패스워드가 일치하는지 확인
    elseif(!password_verify($requestedData['password'], $user->setUserData($requestedData['email'])->getPassword())) {
        $returnData['alertType'] = 'warning';
        $returnData['msg'] = "Password doesn't match!";
        
        echo json_encode($returnData);

        exit;
    }
    //4. 전부 확인이 끝났을 때 세션을 채워넣고 리스펀드.
    else {
        $session_helper->setSession([
            'id' => $user->getUserId(),
            'email' => $user->getEmail(),
            'nickname' => $user->getNickname(),
            'position' => $user->getPosition()
        ]);

        $returnData['alertType'] = 'success';
        $returnData['msg'] = "Login Success!";
        
        echo json_encode($returnData);

        exit;
    }
    

