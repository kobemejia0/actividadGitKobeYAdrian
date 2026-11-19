<?php
/*
Por medio de las cookies guardar que usuario entra en que libro
mapa (clave valor) donde se guarden los libros que que visita el usuario
*/

//inciamos la sesion para solo poder acceder a esta página si se han loggeado
session_start();

require_once 'conexion.php';

//Para evitar accesos indeseados comprobamos que exista la sesion
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

//incluimos el contador del libro∫
require_once 'contador_visitas_libro.php';

$libro = null;
$error = null;

//variable que te lleva al index correcto
$urlCorrecta = str_contains($_SESSION['usuario'] ?? '', 'admin') ? 'index_admin.php' : 'index.php';

//validamos si es admin o no para aplicar el css correcto
$esAdmin = str_contains($_SESSION['usuario'], 'admin');
$clasePanel = $esAdmin ? 'panel-admin' : 'panel-cliente';


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $conexion = conectar();

        //Mostrar la información del libro mediante su isbn
        //recibimos el parametro por get
        if (isset($_GET['ISBN_Libro'])) {
            //guardamos en una variable el isbn 
            $isbn_seleccionado = $_GET['ISBN_Libro'];
            //Realizamos la consulta SQL para obtener los datos de ese libro
            //Para evitar problemas de inyeccion de código dejamos al final el ?
            $consultaLibro = "SELECT ISBN, Titulo, Sinopsis, Portada from libros where ISBN = ?";
            //preparamos la consulta
            $statement = $conexion->prepare($consultaLibro);
            //la ejecutamos y pasamos el parametro como array para evitar errores
            $statement->execute([$isbn_seleccionado]);
            //Al ser solo un libro usamos fetch
            $libro = $statement->fetch();
        }
    } catch (PDOException $e) {
        $error = "Error en la base de datos: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="<?= $clasePanel ?>">
    <div class="contenedor-libro">
        <?php if (!empty($libro)): ?>
            <h3>Libro seleccionado</h3>
            <img src="<?= htmlspecialchars($libro['Portada']) ?>" 
                     alt="Portada de <?= htmlspecialchars($libro['Titulo']) ?>" 
                     style="max-width: 200px; height: auto; margin-bottom: 15px;">
            <p><?= htmlspecialchars($libro['Titulo']) ?></p>
            <p><strong>ISBN:</strong><?= htmlspecialchars($libro['ISBN']) ?></p>
            <p><strong>Sinopsis:</strong><?= htmlspecialchars($libro['Sinopsis']) ?></p>
            <p>Has visitado este libro <strong><?= $numVisitasLibro ?></strong> <?= $numVisitasLibro === 1 ? 'vez' : 'veces' ?>.</p>
        <?php endif; ?>
        <a href="<?= $urlCorrecta ?>">Volver</a>
    </div>
    
</body>

</html>