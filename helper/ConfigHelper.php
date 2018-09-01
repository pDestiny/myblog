<?php

class ConfigHelper {
    private $dsn = 'mysql:dbname=MyDB;host=pdestiny.xyz';
    private $username = 'myName';
    private $password = 'myPassword';

    public function getDsn() {
        return $this->dsn;
    }
    public function getUsername() {
        return $this->username;
    }
    public function getPassword() {
        return $this->password;
    }
}