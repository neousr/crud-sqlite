<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit;
}

require_once '../includes/functions.php';
require_once '../src/Db.php';

$email = $error = null;

if (array_key_exists('email', $_POST)) {
    $email = escape( $_POST['email'] );
}

// Validación del id de usuario
if ( !$email ) {
    $error = 'Este campo es requerido.';
} elseif ( !validateEmail($email) ) {
    $error = 'El correo electrónico no es válido.';
} else {
    $rows = Db::query('SELECT email FROM user WHERE email = ? LIMIT 1;', $email);
    if ( $rows ) { // if (!$rows) = count($rows) === 0,
        $error = 'Este correo electrónico ya se encuentra registrado.';
    }
}

if ( $error ) {
    echo $error;
} else {
    echo 'Ok';
}