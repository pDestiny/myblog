<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once "autoloader.php";
    require_once __DIR__ . '/../vendor/autoload.php';

    WhoopsHelper::register();

    $toEmail = isset($_POST['email']) ? $_POST['email'] : null;

    $returnMsg = [
        'alertType' => '',
        'msg' => ''
    ];

    $user = new UserModel;

    if(is_null($toEmail)) {
        (new MustacheHelper)->setTemplate('error')->render([
            'errorCode' => '403',
            'errorMsg' => 'Forbidden'
        ]);
        exit;
    } elseif(empty($toEmail)) {

        $returnMsg['alertType'] = "info";
        $returnMsg['msg'] = "Email form was not filled";

        echo json_encode($returnMsg);
        exit;
    } elseif(!preg_match("/^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i", $toEmail)) {
        $returnMsg['alertType'] = "danger";
        $returnMsg['msg'] = "Invalid Email";

        echo json_encode($returnMsg);
        exit;
    } elseif($user->isEmailExists($toEmail) == 0) {
        $returnMsg['alertType'] = "warning";
        $returnMsg['msg'] = "Email doesn't exist";

        echo json_encode($returnMsg);
        exit;
    } 
    else {
        $pw_recover_hashed = (new UserModel)
            ->geneartePwRecoverCd()
            ->savePwRecoverCd($toEmail);
        
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.naver.com;smtp..com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'myMail@naver.com';                 // SMTP username
            $mail->Password = 'mypassword';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
        
            //Recipients
            $mail->setFrom('myMail@naver.com');
            $mail->addAddress($toEmail);     // Add a recipient
            
            //Attachments
            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = "SJ's Blog Password Find Service";
            $mail->Body    = (new MustacheHelper)
                ->setTemplate('email')
                ->getTemplate()
                ->render([
                    'email' => $toEmail,
                    'pw_recover_cd' => $pw_recover_hashed
                ]);
        
            $mail->send();
            
            $returnMsg['alertType'] = "success";
            $returnMsg['msg'] = "We sent a email to your email address. Check your email.";

            echo json_encode($returnMsg);
            exit;

        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

    }
    /* 비밀번호 교체 로직
    비밀번호를 교체하여 저장한 다음 교체한 비밀번호를 메일로 보내준다.

    1. 비밀번호를 숫자와 영문자가 섞인 랜덤 글자를 생성해 낸다. (12글자)
    
    2. 비밀번호 회복 코드를 생성해 요청하는 이메일의 PW_RECOVER_CD에 저장한다.

    3. 헤쉬된 비밀번호 회복코드를 이메일을 통해 링크로 저장한다.

    4. 사용자가 자신의 메일로 가서 헤쉬된 코드와 이메일이 적힌 링크를 클릭한다.

    5. 사용자가 자신의 이메일에서 링크를 클릭하였으니 본인인 것이 확인 되었다고 판단한다.

    6. resetPassword 에서 이메일과 PW_RECOVER_CD가 일치할 경우에 비밀번호를 변경할 수 있는 폼을 보여준다.

    7. 사용자가 폼을 작성하고 제출하면 원래 있던 이메일의 PW_RECOVER_CD는 삭제하고 새로 생성된 패스워드를 입력한다.

    8. 위의 모든 준비가 끝났을 경우에 home.php 로 redirection 한다.
    // $mailer = new MailerHelper($toEmail, "Your account has been updated!", "<h1>hihi</h1>");

    // $mailer->sendMail();

    */

    