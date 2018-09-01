<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

class MailerHelper {
    private $to;
    private $from;
    private $subject;
    private $body;
    private $user;
    private $mailer;

    public function __construct($to, $subject, $body) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->user = new UserModel;
        $this->mailer = new PHPMailer(true);
        $this->mailer->SMTPDebug = 0;                                 // Enable verbose debug output
        $this->mailer->isSMTP();                                      // Set mailer to use SMTP
        $this->mailer->Host = 'smtp.naver.com;smtp.gmail.com';  // Specify main and backup SMTP servers
        $this->mailer->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mailer->Username = 'myName@naver.com';                 // SMTP username
        $this->mailer->Password = 'myPassword';                           // SMTP password
        $this->mailer->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $this->mailer->Port = 465;
        $this->mailer->setFrom('myName@naver.com');
    }

    public function sendMail() {
        $headers = [];
        $headers[] = "From: {$this->from}";
        $headers[] = "Content-type: text/html; charset=utf-8";

        mail($this->to, $this->subject, $this->body, implode("\r\n", $headers));
    }

    public function sendMailToAdmin() {
        $admins = $this->user->getAdminUsers();
        $Tos = [];
        foreach($admins as $row) {
            $Tos[] = $row['EMAIL'];
        }

        $headers = [];
        $headers[] = "From: {$this->from}";
        $headers[] = "Content-type: text/html; charset=utf-8";

        $this->mailer->addAddress(implode(', ', $Tos));

        $this->mailer->isHTML(true);
        $this->mailer->Subject = $this->subject;
        $this->mailer->Body = $this->body;

        $this->mailer->send();
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setFrom($from) {
        $this->from = $from;
    }
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    public function setBody($body) {
        $this->body = $body;
    }

    public function getTo() {
        return $this->to;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getBody() {
        return $this->body;
    }
    
}