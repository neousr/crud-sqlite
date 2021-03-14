<?php

require_once '../includes/config.php';

$user = getUserByGetId();

// Format DateTime
$user['creado'] = formatDateTime($user['creado']);
$user['modificado'] = formatDateTime($user['modificado']);

$template = DOCUMENT_ROOT . '/templates/user/detail.html';

$flashes = null;
if (Flash::hasFlashes()) {
    $flashes = Flash::getFlashes();
}

require_once DOCUMENT_ROOT . '/templates/index.html';