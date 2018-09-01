<?php

    require_once __DIR__ . "/../vendor/autoload.php";


    class WhoopsHelper {
        public static function register() {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }
    }
    
    