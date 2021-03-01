<?php

require_once '../includes/config.php';

$user = getUserByGetId();

require_once '../src/Flash.php';

$template = '../templates/user/detail.html';

$flashes = null;
if (Flash::hasFlashes()) {
    $flashes = Flash::getFlashes();
}

require_once '../templates/index.html';