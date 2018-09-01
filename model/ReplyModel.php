<?php

require_once __DIR__ . '/../helper/DatabaseHelper.php';

class ReplyModel {
    private $reply_id;
    private $dash_id;
    private $user_id;
    private $content;
    private $reply_to;
    private $reply_level;
    private $db;

    public function __construct() {
        $this->db = new DatabaseHelper();
    }

    //getter setter
    public function setDashId($dash_id) {
        $this->dash_id = Utils::h($dash_id);
    }
    public function setUserId($user_id) {
        $this->user_id = Utils::h($user_id);
    }

    public function setContent($content) {
        $this->content = nl2br(Utils::h($content));
    }

    public function setReplyTo($reply_to) {
        $this->reply_to = Utils::h($content);
    }
    
    public function setReplyLevel($reply_level) {
        $this->reply_level = Utils::h($reply_level);
    }


    public function getDashId() {
        return $this->dash_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getContent() {
        return $this->content;
    }

    public function getReplyTo() {
        return $this->reply_to;
    }
    
    public function getReplyLevel() {
        return $this->reply_level;
    }

    public function insertReply(
        $dash_id, 
        $user_id, 
        $content, 
        $reply_to, 
        $reply_level,
        $reply_to_user
    ) {
        $sql = "INSERT INTO REPLY (DASH_ID, USER_ID, CONTENT, REPLY_TO, REPLY_LEVEL, REPLY_TO_USER) 
                VALUES (:DASH_ID, :USER_ID, :CONTENT, :REPLY_TO, :REPLY_LEVEL, :REPLY_TO_USER)";
                
        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":DASH_ID" => $dash_id,
            ":USER_ID" => $user_id,
            ":CONTENT" => $content,
            ":REPLY_TO" => $reply_to,
            ":REPLY_LEVEL" => $reply_level,
            ":REPLY_TO_USER"=> $reply_to_user 
        ]);
    }

    public function getReplise($dashboard_id) {
        $result = [];

        $sql = "SELECT R.REPLY_ID, R.DASH_ID, U.NICKNAME, R.CONTENT, R.REPLY_TO, R.REPLY_LEVEL, R.REPLY_TO_USER  FROM REPLY AS R, USERS AS U WHERE R.REPLY_LEVEL='1' AND R.USER_ID = U.USER_ID AND R.DASH_ID = :DASH_ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ':DASH_ID' => $dashboard_id
        ]);

        while($row = $this->db->getDataByRow(PDO::FETCH_ASSOC)) {
            $obj = new stdClass;
            $row['REPLY_REPLY'] = [];
            $obj->row = $row;
            $result[] = $obj;
            $this->replyTraverse($obj, $row['REPLY_ID'], $dashboard_id);
        }

        return $result;
    }

    private function replyTraverse(stdClass $reply_obj, $reply_id, $dashboard_id) {

        $db = new DatabaseHelper;

        $sql = "SELECT R.REPLY_ID, R.DASH_ID, U.NICKNAME, R.CONTENT, R.REPLY_TO, R.REPLY_TO_USER, R.REPLY_LEVEL  FROM REPLY AS R, USERS AS U WHERE R.REPLY_TO = :REPLY_TO AND R.USER_ID = U.USER_ID AND R.DASH_ID = :DASH_ID";
        
        $db->setSQL($sql);

        $db->bindValues([
            ":REPLY_TO" => $reply_id,
            ":DASH_ID" => $dashboard_id
        ]);

        while($row = $db->getDataByRow(PDO::FETCH_ASSOC)) {
            $newObj = new stdClass;
            $newObj->row = $row;
            $reply_obj->row['REPLY_REPLY'][] = $newObj;
            $newObj->row['REPLY_REPLY'] = [];

            $this->replyTraverse($newObj, $row['REPLY_ID'], $dashboard_id);
        }
    }

    public function getLastInsertId() {
        return $this->db->getLastInsertId();
    }

    public function getReplyUserId($reply_id) {
        $sql = 'SELECT USER_ID FROM REPLY WHERE REPLY_ID = :REPLY_ID';
        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":REPLY_ID" => $reply_id
        ]);

        return $this->db->getDataByRow(PDO::FETCH_ASSOC)['USER_ID'];
    }
    
    public function deleteReply($reply_id) {
        $sql = 'DELETE FROM REPLY WHERE REPLY_ID = :REPLY_ID';

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":REPLY_ID" => $reply_id
        ]);
    }

    public function updateContent($reply_id, $content) {
        $sql = 'UPDATE REPLY SET CONTENT = :CONTENT WHERE REPLY_ID = :REPLY_ID';
        
        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":CONTENT" => $content,
            ":REPLY_ID" => $reply_id
        ]);
    }

}