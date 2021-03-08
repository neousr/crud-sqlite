<?php

date_default_timezone_set('AMERICA/ARGENTINA/BUENOS_AIRES');
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);
mb_internal_encoding('UTF-8');
set_exception_handler('handleException');

require_once '../src/Db.php';
require_once '../src/NotFoundException.php';
require_once '../src/Flash.php';
require_once '../src/Token.php';

require_once 'constants.php';
require_once 'functions.php';

session_start();

function handleException($e) {
    $extra = ['message' => $e->getMessage()];
    $flashes = null;
    if ($e instanceof NotFoundException) {
        header('HTTP/1.0 404 Not Found');
        $template = '../templates/error/404.html';
    } else {
        // TODO log exception
        header('HTTP/1.1 500 Internal Server Error');
        $template = '../templates/error/500.html';
    }
    require_once '../templates/index.html';
}