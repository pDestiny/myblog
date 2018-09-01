<?php 

class SessionHelper {
    private $id;
    private $email;
    private $nickname;
    private $position;

    public function __construct() {
        
        $this->id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
        $this->email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
        $this->nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : null;
        $this->position = isset($_SESSION['position']) ? $_SESSION['position'] : null;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getNickname() {
        return $this->nickname;
    }

    public function getPosition() {
        return $this->position;
    }

    public function isAdmin() {
        return $this->position === 'ADMIN';
    }

    public function isLogined() {
        if(is_null($this->id) && is_null($this->email) && is_null($this->nickname) && is_null($this->position)) {
            return false;
        } else {
            return true;
        }
    }

    public function setSession(array $session_data) {
        $column = ['id', 'email', 'nickname', 'position'];

        foreach($column as $row) {
            if(isset($session_data[$row])) {
                $this->$row = $session_data[$row];
                $_SESSION[$row] = $this->$row;
            } else {
                throw new Exception("key entered out of bound between id, email, nickname, position");
            }
        }
    }
    public function destroySession() {
        $_SESSION = array();
        $this->id = null;
        $this->email = null;
        $this->nickname = null;
        $this->position = null;
    }

    public function regenerateSessionId() {
        session_regenerate_id(true);
    }
    
    public function updatePartialSession(string $data_name, $data) {

        $session_category = ['id', 'email', 'nickname', 'position'];

        if(array_search($data_name, $session_category) !== false) {

            $_SESSION[$data_name] = $data;

        } else {
            trigger_error("data_name doesn't exist in session data category");
        }
    }
}