<?php
require_once __DIR__ . "/../autoloader.php";

WhoopsHelper::register();

session_start();

$text = isset($_GET['text']) ? $_GET['text'] : null; 

$parser = new MarkParserHelper;

$session_helper = new SessionHelper;

$m = new MustacheHelper;

if(!$session_helper->isAdmin() || !$session_helper->isLogined()) {
    $m->setTemplate('error')->render([
        'errorCode' => '403',
        'errorMsg' => 'Forbidden'
    ]);
    
    exit;
} else {
    $parser->setOriginText($text);
    $parser->setParsedText();

    $returnData = [
        'parsedText' => $parser->getParsedText()
    ];

    echo json_encode($returnData);
}