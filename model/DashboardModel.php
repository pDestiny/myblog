<?php

require_once __DIR__ . '/../helper/DatabaseHelper.php';
require_once __DIR__ . '/../helper/UtilsHelper.php';

class DashboardModel {
    private $id;
    private $title;
    private $subtitle;
    private $nickname;
    private $written;
    private $content;
    private $db;

    public function __construct() {
        $this->db = new DatabaseHelper();
    }
    
    //getter setter 
    public function setTitle($title) {
        $this->title = Utils::h($title);
    }

    public function setSubtitle($subtitle) {
        $this->subtitle = Utils::h($subtitle);
    }

    public function setNickname($nickname) {
        $this->nickname = Utils::h($nickname);
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getSubtitle() {
        return $this->subtitle;
    }

    public function getNickname() {
        return $this->nickname;
    }

    public function getContent() {
        return $this->content;
    }
    
    //필요한 데이터에 대해 method를 추가한다.
    public function saveArticle(string $title, string $subtitle, string $writer_email, string $originText, string $parsedText) {
        $select_user_id = "SELECT USER_ID FROM USERS WHERE EMAIL = :EMAIL";

        $this->db->setSQL($select_user_id);
        $this->db->bindValues([
            ":EMAIL" => $writer_email
        ]);
        $id = $this->db->getDataByRow(PDO::FETCH_ASSOC)['USER_ID'];

        $insert_dashboard = "INSERT INTO DASHBOARD (TITLE, SUBTITLE, NICKNAME, MD_CONTENT, HTML_CONTENT) VALUES(:TITLE, :SUBTITLE, :NICKNAME, :MD_CONTENT, :HTML_CONTENT)";

        $this->db->setSQL($insert_dashboard);

        $this->db->bindValues([
            ":TITLE" => $title,
            ":SUBTITLE" => $subtitle,
            ":NICKNAME" => $id,
            ":MD_CONTENT" => $originText,
            ":HTML_CONTENT" => $parsedText
        ]);
    }

    public function selectArticles() {
        $sql = "SELECT ID, TITLE, SUBTITLE, NICKNAME, WRITTEN FROM DASHBOARD";
        
        $this->db->setSQL($sql);

        $this->db->bindValues([]);

        return $this->db->getDataByArr();
    }

    public function deleteArticle($id) {
        $sql = "DELETE FROM DASHBOARD WHERE ID = :ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":ID" => $id
        ]);
    }

    public function selectArticle($id) {
        $sql = "SELECT * FROM DASHBOARD WHERE ID = :ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":ID" => $id
        ]);

        return $this->db->getDataByRow(PDO::FETCH_ASSOC);
    }
    public function updateArticle($id, $title, $subtitle, $md_content, $html_content){
        $sql = "UPDATE DASHBOARD SET TITLE = :TITLE, SUBTITLE = :SUBTITLE, MD_CONTENT = :MD_CONTENT, HTML_CONTENT = :HTML_CONTENT WHERE ID = :ID";

        $this->db->setSQL($sql);

        $this->db->bindValues([
            ":TITLE" => $title,
            ":SUBTITLE" => $subtitle,
            ":MD_CONTENT" => $md_content,
            ":HTML_CONTENT" => $html_content,
            ":ID" => $id
        ]);
    }
    public function selectArticlesByLimit($start, $end) {
        $sql = "SELECT DASH.ID, DASH.TITLE, DASH.SUBTITLE, USERS.NICKNAME, DASH.WRITTEN FROM DASHBOARD AS DASH, USERS WHERE DASH.NICKNAME = USERS.USER_ID ORDER BY DASH.WRITTEN DESC LIMIT ". $start . ", ". $end;

        $this->db->setSQL($sql);

        $this->db->bindValues([]);
        
        return $this->db->getDataByArr();
    }
}