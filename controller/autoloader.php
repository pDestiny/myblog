<?php 

spl_autoload_register(function($className) {
   if(strpos($className, 'Helper') !== false) {
        require_once __DIR__ . '/../helper/' . $className . '.php';
   } elseif(strpos($className, 'Model') !== false) {
        require_once __DIR__ . '/../model/' . $className . '.php';
   }
});