<?php

require_once '../includes/config.php';

$user = [
    'id_user' => null,
    'apellido' => null, 'nombre' => null,
    'email' => null, 'telefono' => null,
];

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
        header('Location: index.php');
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
        // Unique
        elseif (existeEmailUser( $user['email'] )) {
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
            $q = 'INSERT INTO user (apellido, nombre, email, telefono, creado, modificado) VALUES(?, ?, ?, ?, ?, ?);';
            $res = Db::query($q, ucwords($user['apellido']), ucwords($user['nombre']), $user['email'], $user['telefono'], $now, $now);
            if ($res) {
                Flash::addFlash('Los datos fueron guardados correctamente.', 'success');

                $user['id_user'] = Db::getInstance()->lastInsertId();
                
                header('Location: detail.php?id_user=' . $user['id_user']);
                exit;
            } else {
                Flash::addFlash('Lo sentimos, no pudimos guardar el registro.', 'danger');
                header('Location: index.php');
                exit;
            }
        }
    }
}

$template = DOCUMENT_ROOT . '/templates/user/add.html';

$flashes = null;
if (Flash::hasFlashes()) {
    $flashes = Flash::getFlashes();
}

require_once DOCUMENT_ROOT . '/templates/index.html';