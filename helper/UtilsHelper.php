<?php
require_once __DIR__ . '/../helper/MustacheHelper.php';

Class UtilsHelper {
    public static function h($str) {
        if(is_array($str)) {
            return array_map('h', $str);
        } else {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }
    }

    public static function showErrorScreen($errorCode, $errorMsg) {
        (new MustacheHelper)->setTemplate('error')->render([
            'errorCode' => $errorCode,
            'errorMsg' => $errorMsg
        ]);         
    }

    public static function sendAjaxMsg($alertType, $msg) {
        $returnMsg = [
            'alertType' => $alertType,
            'msg' => $msg
        ];

        echo json_encode($returnMsg);
    }
}