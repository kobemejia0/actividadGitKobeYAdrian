<?php
//Generar un PDO (PHP DATA OBJECTS) para la conexión a la base de datos de biblioteca

//Definimos las variables de conexion
define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca');

//define('DB_USER','cliente');
//define('DB_PASS','cliente');

//Creamos la conexión PDO
//pasamos por parametro el usuario que se conectará a la base de datos
function conectar(string $DB_USER = 'cliente', string $DB_PASS = 'cliente'): PDO{
    static $conexion = null;

    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';

    return $conexion ?? new PDO(
        $dsn,
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //modo de error: excepciones para usar try/catch
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //deevuelve los resultados como array asociativo
            PDO::ATTR_EMULATE_PREPARES => false, //evita inyecciones sql
        ]
    );
}
