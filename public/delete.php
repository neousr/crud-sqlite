<?php

require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit;
}

$id_user = null;
$error = null;

if (array_key_exists('id_user', $_POST)) {
    $id_user = escape( $_POST['id_user'] );
}

// Validación del id de usuario
if ( !$id_user ) {
    $error = 'El parámetro "id_user" no ha sido encontrado o tiene un valor vacío.';
} elseif ( !is_numeric($id_user) ) {
    $error = 'El parámetro "id_user" contiene un identificador no válido.';
} else {
    $rows = Db::query('SELECT id_user FROM user WHERE eliminado = 0 AND id_user = ? LIMIT 1;', $id_user);
    if ( !$rows ) {
        $error = 'La búsqueda ha devuelto ningún resultado.';
    }
}

if ( $error ) {
    echo $error;
    exit;
}

$res = Db::query('UPDATE user SET eliminado = 1 WHERE id_user = ?;', $id_user);

if ( !$res ) {
    echo 'Error: no pudimos eliminar el registro de usuario.';
    exit;
}

require_once '../src/Flash.php';

Flash::addFlash('El registro fue eliminado correctamente.', 'success');

echo 'Ok';