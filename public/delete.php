<?php
/**
 * Para no requerir archivos que contienen rutinas que no vamos a utilizar,
 * este archivo solo requiere lo necesario para ejecutarse correctamente, solo
 * si se accede a el a través del método post.
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit;
}

require_once '../includes/functions.php';
require_once '../src/Db.php';

$id_user = $error = null;

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
    echo 'Error: no pudimos eliminar el registro.';
    exit;
}

require_once '../src/Flash.php';

Flash::addFlash('El registro fue eliminado correctamente.', 'success');

echo 'Ok';

// if (!$rows) = count($rows) === 0