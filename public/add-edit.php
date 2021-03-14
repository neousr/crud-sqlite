<?php

require_once '../includes/config.php';

$user = null;
$errors = [];

$edit = array_key_exists('id_user', $_GET);

if ($edit) {
    $user = getUserByGetId();
} else {
    $user = [
        'id_user' => null,
        'apellido' => null, 'nombre' => null,
        'email' => null, 'telefono' => null,
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (array_key_exists('token', $_POST)) {
        if (!Token::validate(escape($_POST['token']))) {
            // Si el token CSRF que enviaron no coincide con el que enviamos.
            // Re direccionar al logout
            // echo 'Te tengo que re dirigir porque presionaste ctrl + U!';
            // exit;
            header('Location: index.php');
            exit;
        }
    }
    // No existe la key token
    else {
        // Re direccionar al logout
        header('Location: index.php');
        exit;
    }

    if (array_key_exists('cancelar', $_POST)) {
        // Re dirigimos al usuario a la vista de detalle. Para lograr esto sin usar la variable de sesión.
        // Es necesario en el action del form de edición colocar un símbolo numeral (#)
        if ($user['id_user'] === null) {
            header('Location: index.php');
            exit;
        }
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
        } elseif ( validateEmail( $user['email'] ) ) {
            $res = null;
            if ($user['id_user'] === null) {
                // Unique
                $res = existeEmailUser( $user['email'] );
            } else {
                // Unique, enviamos ambos parámetros para poder editar el campo e-mail
                $res = existeEmailUser( $user['email'], $user['id_user'] );
            }
            if ($res) {
                $errors['email'] = str_replace(':f', 'correo electrónico', $messages['unique'] );
            }
        } else {
            $errors['email'] = $messages['valid_email'];
        }

        // Teléfono
        if ( !$user['telefono'] ) {
            $errors['telefono'] = $messages['required'];
        } elseif ( !validatePhone( $user['telefono'] ) ) {
            $errors['telefono'] = $messages['valid_phone'];
        }
        
        // Si no existen errores en el array
        if( empty($errors) ) {
            $user = guardar($user);
            Flash::addFlash('Los datos fueron guardados correctamente.', 'success');
            // Redirect to detail
            // Si insertamos recuperamos el último id insertado,
            // Si actualizamos ya contamos con el id de usuario
            header('Location: detail.php?id_user=' . $user['id_user']);
            exit;
        }
    }
}

//

$template = DOCUMENT_ROOT . '/templates/user/add-edit.html';

$flashes = null;
if (Flash::hasFlashes()) {
    $flashes = Flash::getFlashes();
}

require_once DOCUMENT_ROOT . '/templates/index.html';

// 

function guardar(array $user) {
    if ($user['id_user'] === null) {
        return insert($user);
    }
    return update($user);
}

function update(array $user) {
    $now = date('Y-m-d H:i:s');
    // update
    $q = 'UPDATE user SET apellido = ?, nombre = ?, email = ?, telefono = ?, modificado = ? WHERE id_user = ?;';
    $res = Db::query($q, ucwords($user['apellido']), ucwords($user['nombre']), $user['email'], $user['telefono'], $now, $user['id_user']);
    if ( !$res ) {
        throw new NotFoundException('Lo sentimos, no pudimos actualizar el registro.');
    }
    return $user;
}

function insert(array $user) {
    $now = date('Y-m-d H:i:s');
    // insert
    $q = 'INSERT INTO user (apellido, nombre, email, telefono, creado, modificado) VALUES(?, ?, ?, ?, ?, ?);';
    $res = Db::query($q, ucwords($user['apellido']), ucwords($user['nombre']), $user['email'], $user['telefono'], $now, $now);
    if ( !$res ) {
        throw new NotFoundException('Lo sentimos, no pudimos insertar el registro.');
    }
    // Alternativa 1: Asignamos el último id insertado al índice id_user del parámetro array $user
    $user['id_user'] = Db::getInstance()->lastInsertId();
    return $user;
    // Alternativa 2: Devolvemos el nuevo registro insertado con todos los campos
    // return getUserById( Db::getInstance()->lastInsertId() );
}