<?php

require_once __DIR__ . "/../vendor/autoload.php";

class MarkParserHelper {
    private $parser;
    private $originText;
    private $parsedText;
    private $isTextSet = false;

    public function __construct() {
        $this->parser = new Parsedown;
        $this->parser->setSafeMode(true);
        $this->parser->setBreaksEnabled(true);
    }

    public function setOriginText($origin_text) {
        $this->originText = $origin_text;
        $this->isTextSet = true;
    }

    public function setParsedText() {
        if(!$this->isTextSet) throw new Exception('Data for Markdown parasing not prepared');
        else {
            $this->parsedText = $this->parser->text($this->originText);
        }
    }
    public function getOriginText() {
        return $this->originText;
    }
    public function getParsedText() {
        if(!$this->isTextSet) throw new Exception('Data for Markdown parasing not prepared');
        else {
            return $this->parsedText;
        }
    }
}