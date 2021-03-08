<?php

require_once '../includes/config.php';

// Verificar si existen registros
$users = Db::query('SELECT id_user, apellido, nombre, email, telefono FROM user WHERE eliminado = 0;');

$template = DOCUMENT_ROOT . '/templates/user/list.html';

$flashes = null;
if (Flash::hasFlashes()) {
    $flashes = Flash::getFlashes();
}

require_once DOCUMENT_ROOT . '/templates/index.html';