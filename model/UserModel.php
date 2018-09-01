<?php

require_once __DIR__ . '/../helper/DatabaseHelper.php';
require_once __DIR__ . "/../helper/UtilsHelper.php";

class UserModel {
    private $user_id;
    private $email;
    private $nickname;
    private $password;
    private $pw_recover_cd;
    private $position;
    private $db;

    public function __construct() {
        $this->db = new DatabaseHelper;
    }

    //getter setter
    public function setEmail($email) {
        $this->email = filter_var(UtilsHelper::h($email), FILTER_SANITIZE_EMAIL);
    }

    public function setNickname($nickname) {
        $this->nickname = Utils::h($nickname);
    }

    public function setPassword($password) {
        $this->password = password_hash($password);
    }

    public function setPwRecoverCd($code) {
        $this->pw_recover_cd = md5($code);
    }

    public function getUserId() {
        return $this->user_id;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getNickname() {
        return $this->nickname;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getPwRecoverCd() {
        return $this->pw_recover_cd;
    }

    public function getPosition() {
        return $this->position;
    }

    public function isEmailExists($email) {
    
      $sql = "SELECT COUNT(EMAIL) FROM USERS WHERE EMAIL = :EMAIL";

      try {
        $this->db->setSQL($sql);
      
        $this->db->bindValues([':EMAIL' => $email]);
            
        return $this->db->getDataByRow()['COUNT(EMAIL)'];
      } catch(Exception $e) {
          echo "UserModel-isEamilExists : {$e->getMessage()}";
          exit;
      }
    }

    public function isNicknameExists($nickname) {
        $sql = "SELECT COUNT(NICKNAME) FROM USERS WHERE NICKNAME = :NICKNAME";
        try {
            $this->db->setSQL($sql);
            $this->db->bindValues([":NICKNAME" => $nickname]);
            
            return $this->db->getDataByRow(PDO::FETCH_ASSOC)['COUNT(NICKNAME)'];
        } catch(Exception $e) {
            echo "UserModel-isNicknameExists : {$e->getMessage()}";
            exit;
        }
    }

    public function saveUser($email, $nickname, $password_hashed) {
        $sql = "INSERT INTO USERS (EMAIL, NICKNAME, PASSWORD, POSITION) VALUES(:EMAIL, :NICKNAME, :PASSWORD, :POSITION)";
        
        try {
            $this->db->setSQL($sql);

            $saveData = [
                ":EMAIL" => $email,
                ":NICKNAME" => $nickname,
                ":PASSWORD" => $password_hashed,
                ":POSITION" => "USER"
            ];

            $this->db->bindValues($saveData);

            return $this->db->getLastInsertId();
        } catch(Exception $e) {
            echo "UserModel-saveUser : {$e->getMessage()}";
        }
    }

    public function setUserData($email) {
        $sql = "SELECT USER_ID, EMAIL, NICKNAME, PASSWORD, POSITION FROM USERS WHERE EMAIL = :EMAIL";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":EMAIL" => $email
        ]);

        foreach($this->db->getDataByRow(PDO::FETCH_ASSOC) as $key => $value) {
            $key = strtolower($key);
            $this->$key = $value;
        }

        return $this;
    }

    public function getAdminUsers() {
        $sql = "SELECT EMAIL FROM USERS WHERE POSITION = 'ADMIN'";
        $this->db->setSQL($sql);

        $this->db->bindValues([]);

        return $this->db->getDataByArr();
    }

    public function geneartePwRecoverCd()  {
        $randomPickArr = [
            '1','2','3','4','5','6','7','8','9','0', 
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z', 
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z', 
            '!','@','#','$','%','^','&','*','(',')'
            //출처: http://bbaksae.tistory.com/5 [QRD]
        ];

        $randomPw = '';

        for($i = 0; $i < 12; ++$i) {
            $randomPw .= $randomPickArr[rand(0, count($randomPickArr) - 1)];
        }
        $this->pw_recover_cd = $randomPw;
        return $this;
    }

    public function savePwRecoverCd($email) {
        $sql = 'UPDATE USERS SET PW_RECOVER_CD = :PW_RECOVER_CD WHERE EMAIL = :EMAIL';

        $this->db->setSQL($sql);

        $pw_recover_cd_hashed = md5($this->pw_recover_cd);
        $this->db->bindValues([
            ':PW_RECOVER_CD' => $pw_recover_cd_hashed,
            ":EMAIL" => $email
        ]);

        return $pw_recover_cd_hashed;
    }
    public function getPwRecoverCdOf($email){
        $sql = 'SELECT PW_RECOVER_CD FROM USERS WHERE EMAIL = :EMAIL';

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":EMAIL" => $email
        ]);

        return $this->db->getDataByRow(PDO::FETCH_ASSOC);
    }
    public function resetPwRecoverCd($email) {
        $sql = "UPDATE USERS SET PW_RECOVER_CD = '' WHERE EMAIL = :EMAIL";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":EMAIL" => $email
        ]);
    }

    public function deletePwRecoverCd($email) {
        $sql = "UPDATE USERS SET PW_RECOVER_CD = '' WHERE EMAIL = :EMAIL";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':EMAIL' => $email
        ]);
    }
    public function updatePassword($newPassword, $email) {
        $sql = "UPDATE USERS SET PASSWORD = :PASSWORD WHERE EMAIL = :EMAIL";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':PASSWORD' => password_hash($newPassword, PASSWORD_BCRYPT),
            ':EMAIL' => $email
        ]);
    }

    public function selectNickname($email) {
        $sql = "SELECT NICKNAME FROM USERS WHERE EMAIL = :EMAIL";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':EMAIL' => $email
        ]);

        return $this->db->getDataByRow()['NICKNAME'];
    }

    public function updateEmail($old_email, $new_email) {
        $sql = "UPDATE USERS SET EMAIL = :NEW_EMAIL WHERE EMAIL = :OLD_EMAIL";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':NEW_EMAIL' => $new_email,
            ":OLD_EMAIL" => $old_email
        ]);
    }
    public function selectEmail($nickname) {
        $sql = "SELECT EMAIL FROM USERS WHERE NICKNAME = :NICKNAME";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':NICKNAME' => $nickname
        ]);

        return $this->db->getDataByRow()['EMAIL'];
    }
    public function updateNickname($old_nickname, $new_nickname) {
        $sql = 'UPDATE USERS SET NICKNAME = :NEW_NICKNAME WHERE NICKNAME = :OLD_NICKNAME';

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':OLD_NICKNAME' => $old_nickname,
            ':NEW_NICKNAME' => $new_nickname
        ]);
    }

    public function selectEmailAndNickname($user_id) {
        $sql = "SELECT EMAIL, NICKNAME FROM USERS WHERE USER_ID = :USER_ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":USER_ID" => $user_id
        ]);

        return $this->db->getDataByRow(PDO::FETCH_ASSOC);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM USERS WHERE USER_ID = :USER_ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":USER_ID" => $id
        ]);
    }

    public function selectNicknameById($id) {
        $sql = "SELECT NICKNAME FROM USERS WHERE USER_ID = :USER_ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":USER_ID" => $id
        ]);

        return $this->db->getDataByRow(PDO::FETCH_ASSOC)['NICKNAME'];
    }
}