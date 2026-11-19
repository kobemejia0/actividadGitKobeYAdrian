<?php

//iniciamos la sesion
session_start();

//incluimos la conexion a la base de datos
include_once 'conexion.php';

//Verificamos que solo tenga acceso el administrador
if(!isset($_SESSION['usuario']) || !str_contains($_SESSION['usuario'], 'admin')){
    header('Location: index.php');
    exit;
}

//Comprobamos que el formulario se envie por el método correcto
//y que se han enviado los estados por post
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estados'])){
    try {
        //nos conectamos como bibliotecarios
        $conexion = conectar('bibliotecario', 'bibliotecario');

        //preparamos la consulta sql para actualizar los estados
        $SQL = "UPDATE ejemplares SET Estado = :estado WHERE Id_Ejemplar = :id";
        $statement = $conexion->prepare($SQL);

        //Recorremos el array asociativo recibiendo el ID (clave) y el Estado (valor)
        foreach ($_POST['estados'] as $idEjemplar => $nuevoEstado) {
            $statement->execute([
                ':estado' => $nuevoEstado,
                ':id'     => $idEjemplar
            ]);
        }
    } catch (PDOException $e) {
        $error = "Error en la base de datos: " . $e->getMessage();
    }
}
// Redirigimos de vuelta al panel de administración para ver los cambios reflejados
header('Location: index_admin.php');
exit;