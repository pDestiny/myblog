<?php

require_once "ConfigHelper.php";

class DatabaseHelper {
    private $config;
    private $db;
    private $stmt;
    private $isSqlSet = false;

    public function __construct() {
        $this->config = new ConfigHelper;

        try {
            $this->db = new PDO($this->config->getDsn(),
                            $this->config->getUsername(),
                            $this->config->getPassword());
        } catch(PDOException $e) {
            echo "Database error : " . $e->getMessage();
        }
    }

    public function setSQL($sql) {
        
        try {
            $this->stmt = $this->db->prepare($sql);
            if(!$this->stmt) {
                trigger_error("Database error : prepare failed", E_USER_NOTICE);
            }
            $this->isSqlSet = true;
            return $this;
        } catch(Error $e) {
            echo "Database error : <br/>" . $e->getMessage();
        }
    }

    public function bindValues(array $data) {

        try {
            if(!$this->isSqlSet) throw new Exception("SQL is not set yet. please set sql first with setSQL method");
            foreach($data as $key => $value) {
                $this->stmt->bindValue($key, $value);
            }
            if(!$this->stmt->execute()) throw new Exception("SQL execution failed. Check SQL and binding key and value again");
            $this->isSqlSet = false;
        } catch(Exception $e) {
            echo "Database error : " . $e->getMessage();
        }
        
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function getDataByRow($constant = PDO::FETCH_BOTH) {
        return $this->stmt->fetch($constant);
    }

    public function getDataByArr() {
        return $this->stmt->fetchAll();
    }
}