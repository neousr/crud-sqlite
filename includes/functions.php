<?php

function escape(string $s) : string {
    return htmlspecialchars(stripslashes(trim($s)));
}

function getUrlParam($name) {
    if (!array_key_exists($name, $_GET)) {
        throw new NotFoundException('El parámetro URL ' . $name . ' no ha sido encontrado.');
    }
    return escape( $_GET[$name] );
}

function getUserByGetId() {
    $id_user = null;
    try {
        $id_user = getUrlParam('id_user');
    } catch (Exception $e) {
        throw new NotFoundException($e->getMessage());
    }
    if ( !$id_user ) {
        throw new NotFoundException('El parámetro URL id_user tiene un valor vacío.');
    } elseif ( !is_numeric($id_user) ) {
        throw new NotFoundException('El parámetro URL id_user contiene un identificador no válido.');
    } else {
        $user = getUserById($id_user);
        if ($user === null) {
            throw new NotFoundException('La búsqueda ha devuelto ningún resultado.');
        }
    }
    return $user;
}

function getUserById($id_user) {
    // Esta consulta solicita todas las columnas del registro
    $rows = Db::query('SELECT * FROM user WHERE eliminado = 0 AND id_user = ? LIMIT 1;', $id_user);
    if ( !$rows ) {
        return null;
    }
    return $rows[0];
}

/**
 * Devuelve true si el string contiene un carácter numérico positivo
 * false en caso contrario
 */
function isPositiveInt(string $s) : bool {
    if( isInt($s) ) {
        $n = (int) $s;
        if($n > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Válida un string con caracteres numéricos enteros
 */
function isInt(string $s) : bool {
    if ($s === null) {
        return false;
    }
    // Si es un carácter numérico entero
    return preg_match('/^[+-]?\d+$/', $s);
}

function onlyletters($value) {
    return preg_match('/^[A-Za-záéíóúÁÉÍÓÚÑñÜü\s]+$/', $value);
}

function minlength($value, $minlength) {
    $strlen = mb_strlen(trim($value));
    return ($strlen >= $minlength);
}

function maxlength($value, $maxlength) {
    $strlen = mb_strlen(trim($value));
    return ($strlen <= $maxlength);
}

function validateEmail($email) {
    // https://owasp.org/www-community/OWASP_Validation_Regex_Repository
    // $regexEmail = '/^[a-zA-Z0-9_+&*-]+(?:\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,7}$/';
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return preg_match('/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/', $email);
}

function existeEmailUser($email, $id_user = null) {
    $email = strtolower($email);
    $res = null;
    if ($id_user) {
        $res = Db::query('SELECT email FROM user WHERE email = ? AND id_user != ? LIMIT 1;', $email, $id_user);
    } else {
        $res = Db::query('SELECT email FROM user WHERE email = ? LIMIT 1;', $email);
    }
    return ($res) ?? false;
}

/**
 * https://es.stackoverflow.com/questions/136325/validar-tel%C3%A9fonos-de-argentina-con-una-expresi%C3%B3n-regular
 */
function validatePhone($tel) {
    /**
     * sin espacios, puntos u otros símbolos
     */
    if (!preg_match('/^\d+$/', $tel)) {
        return false;
    }
    $re = '/^(?:((?P<p1>(?:\( ?)?+)(?:\+|00)?(54)(?<p2>(?: ?\))?+)(?P<sep>(?:[-.]| (?:[-.] )?)?+)(?:(?&p1)(9)(?&p2)(?&sep))?|(?&p1)(0)(?&p2)(?&sep))?+(?&p1)(11|([23]\d{2}(\d)??|(?(-10)(?(-5)(?!)|[68]\d{2})|(?!))))(?&p2)(?&sep)(?(-5)|(?&p1)(15)(?&p2)(?&sep))?(?:([3-6])(?&sep)|([12789]))(\d(?(-5)|\d(?(-6)|\d)))(?&sep)(\d{4})|(1\d{2}|911))$/D';
    if (preg_match($re, $tel, $match)) {
        return true;
    }
    return false;
}