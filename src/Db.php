<?php

/**
 * La clase Db provee conexión a base de datos
 */
final class Db {

    private $connection;
    private static $_instance = null;

    private function __construct() {
        // $config = Config::getConfig('sqlitedb');
        // $config['dsn'], $config['username'], $config['password']
        // SQLite: dsn => 'sqlite:../db/yourdatabase.db', username => '', password => ''
        // MySQL: dsn => 'mysql:host=localhost:dbname=yourdatabase;charset=utf8mb4', username => 'root', password => ''
        try {
            $this->connection = new PDO('sqlite:../db/db.db', '', '', array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ));
        } catch (PDOException $e) {
            // Está exception se lanzará si la ruta al archivo de db no es la indicada.
            throw new PDOException('Could not connect to database: ' . $e->getMessage());
        }
    }

    /* Devolvemos la conexión
     * El método getConnection no puede ser estático porque no será llamado desde fuera de la clase Db.
     * todo sera gestionado por el único método estático que es getInstance el cual si será llamado desde fuera.
     */
    private function getConnection() {
        return $this->connection;
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    // https://www.php.net/manual/es/language.oop5.magic.php
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    // close connection. The __destruct magic method must be public.
    public function __destruct() {
        $this->connection = null;
    }

    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance->getConnection();
    }

    /**
     * func_get_args ( void ) : array, Obtiene un array de la lista de argumentos de una función.
     * array_slice — Extraer una parte de un array
     */
    public static function query(/* $sql [, ... ] */) {
        // SQL statement
        $sql = func_get_arg(0);

        // parameters, if any
        $parameters = array_slice(func_get_args(), 1);

        $stmt = self::getInstance()->prepare($sql);

        if ($stmt === false) {
            self::throwDbError(self::getInstance()->errorInfo());
        }

        $result = $stmt->execute($parameters);

        if ($result === false) {
            self::throwDbError(self::getInstance()->errorInfo());
        }

        // Determining the Type of a Statement http://www.kitebird.com/articles/php-pdo.html
        if ($stmt->columnCount() > 0) {
            // return result set's rows, if query was SELECT
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // if query was DELETE, INSERT, or UPDATE
        else {
            // return number of rows affected
            // Nota: si el número de filas que fueron afectadas por una consulta DELETE, INSERT O UPDATE es mayor a 1,
            // la función devolverá falso aún cuando los cambios si se realizarón en la base de datos. Normalmente solo
            // afectamos un solo registro.
            return ($stmt->rowCount() == 1); // true o false
        }
    }

    private static function throwDbError(array $errorInfo) {
        // USER log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }
}
