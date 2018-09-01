<?php

require_once __DIR__ . "/../vendor/autoload.php";

class MustacheHelper {
    private $mustache;
    private $template;

    public function __construct() {
        $this->mustache = new Mustache_Engine(
            array(
                'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../view'),
                'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../view/partials'),
                'charset' => 'UTF-8',
                'escape' => function($value) {
                    return $value;
                }
            )
        );
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getMustache() {
        return $this->mustache;
    }

    public function setTemplate($templateName) {
        $this->template = $this->mustache->loadTemplate($templateName);
        return $this;
    }

    public function render(array $data = null) {
        if($data === null) {
            echo $this->template->render();
        } else {
            echo $this->template->render($data);
        }
    } 
}