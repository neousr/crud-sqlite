<?php

require_once '../includes/config.php';
require_once '../src/Flash.php';
require_once '../src/Token.php';

$user = getUserByGetId();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (array_key_exists('token', $_POST)) {
        if (!Token::validate(escape($_POST['token']))) {
            // Si el token CSRF que enviaron no coincide con el que enviamos.
            // redirect('/usuario_logout.php');
            header('Location: index.php');
            exit;
        }
    }
    // No existe la key token
    else {
        // redirect('/usuario_logout.php');
        header('Location: index.php');
        exit;
    }

    if (array_key_exists('cancelar', $_POST)) {
        // Re dirigimos al usuario a la vista de detalle. Para lograr esto sin usar la variable de sesión.
        // Es necesario en el action del form de edición colocar un símbolo numeral (#)
        header('Location: detail.php?id_user=' . $user['id_user']);
        exit;
    } elseif (array_key_exists('guardar', $_POST)) {
        foreach ($user as $key => $value) {
            if (array_key_exists($key, $_POST)) {
                $user[$key] = escape( $_POST[$key] );
            }
        }

        /**
         * Validaciones
         */

        // Apellido
        if ( !$user['apellido'] ) {
            $errors['apellido'] = $messages['required'];
        } elseif ( !onlyletters( $user['apellido'] ) ) {
            $errors['apellido'] = $messages['onlyLetters'];
        } elseif ( !minlength( $user['apellido'], LONGITUD_MINIMA) ) {
            $errors['apellido'] = $messages['minLength'];
        } elseif ( !maxlength($user['apellido'], LONGITUD_MAXIMA) ) {
            $errors['apellido'] = $messages['maxLength'];
        }

        // Nombre
        if ( !$user['nombre'] ) {
            $errors['nombre'] = $messages['required'];
        } elseif ( !onlyletters( $user['nombre'] ) ) {
            $errors['nombre'] = $messages['onlyLetters'];
        } elseif ( !minlength( $user['nombre'], LONGITUD_MINIMA) ) {
            $errors['nombre'] = $messages['minLength'];
        } elseif ( !maxlength($user['nombre'], LONGITUD_MAXIMA) ) {
            $errors['nombre'] = $messages['maxLength'];
        }

        // E-mail
        if ( !$user['email'] ) {
            $errors['email'] = $messages['required'];
        } elseif ( !validateEmail( $user['email'] ) ) {
            $errors['email'] = $messages['valid_email'];
        }
        // Unique, enviamos ambos parámetros para poder editar el campo e-mail
        elseif (existeEmailUser( $user['email'], $user['id_user'] )) {
            $errors['email'] = str_replace(':f', 'correo electrónico', $messages['unique'] );
        }

        // Teléfono
        if ( !$user['telefono'] ) {
            $errors['telefono'] = $messages['required'];
        } elseif ( !validatePhone( $user['telefono'] ) ) {
            $errors['telefono'] = $messages['valid_phone'];
        }
        
        // Si no existen errores en el array
        if( empty($errors) ) {
            $now = date('Y-m-d H:i:s');
            // guardar
            $q = 'UPDATE user SET apellido = ?, nombre = ?, email = ?, telefono = ?, modificado = ? WHERE id_user = ?;';
            $res = Db::query($q, ucwords($user['apellido']), ucwords($user['nombre']), $user['email'], $user['telefono'], $now, $user['id_user']);
            if ($res) {
                Flash::addFlash('Los datos fueron guardados correctamente.', 'success');
                header('Location: detail.php?id_user=' . $user['id_user']);
                exit;
            } else {
                Flash::addFlash('Lo sentimos, no pudimos guardar el registro.', 'danger');
                header('Location: detail.php?id_user=' . $user['id_user']);
                exit;
            }
        }
    }
}

$template = DOCUMENT_ROOT . '/templates/user/edit.html';

$flashes = null;
if (Flash::hasFlashes()) {
    $flashes = Flash::getFlashes();
}

require_once DOCUMENT_ROOT . '/templates/index.html';