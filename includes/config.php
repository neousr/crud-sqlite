<?php

require_once '../includes/functions.php';
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);
mb_internal_encoding('UTF-8');
set_exception_handler('handleException');

require_once 'constants.php';

session_start();