<?php
//inciamos la sesion para solo poder acceder a esta página si se han loggeado
session_start();

require_once 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Incluimos la lógica del contador
require_once 'contador_visitas.php';

// Comprobar si NO existe la variable de sesión
if (!isset($_SESSION['usuario'])) {
    // Si no está autenticado, lo mandamos de vuelta al login
    header('Location: login.php');
    exit;
}
// Si es un administrador que entró a index.php, lo enviamos a su página correspondiente
if (str_contains($_SESSION['usuario'], 'admin')) {
    header('Location: index_admin.php');
    exit;
}

$ejemplares = [];
$error = null;
$libro = null;
try {
    $conexion = conectar("cliente", "cliente");
    //como recomendacion, no usar select * sino cada argumento para poder validarlo
    $consultaSQL = "SELECT DISTINCT  e.ISBN_Libro,  e.Estado, l.Portada 
                FROM ejemplares e 
                JOIN libros l ON e.ISBN_Libro = l.ISBN
                where e.Estado = 'Disponible'";
    /*Las consultas hechas con query deben hacerse solo para consultas estaticas porque
    se envian y se ejecutan a la vez y puede tener problemas de seguridad
    Si la sentencia sql viene de un formulario o de fuera, se usa prepare + execute*/
    $statement = $conexion->query($consultaSQL);

    $ejemplares = $statement->fetchAll();

} catch (PDOException $e) {
    $error = "Error en la base de datos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Mostrar ejemplares</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="panel-cliente">
    <div class="contenedor-cliente">
        <h1>Hola <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
        <h3>Ejemplares disponibles</h3>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (empty($ejemplares)): ?>
            <p> No hay ejemplares disponibles </p>
        <?php else: ?>
            <!--Creamos una tabla para mostrar los datos-->
            <table>
                <thead>
                    <tr>
                        <!-- <th>ID Ejemplar</th> -->
                        <th>ISBN libro</th>
                        <th>Estado</th>
                        <th>Portada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ejemplares as $ejemplar): ?>
                        <tr>
                            
                            <td><a
                                    href="libro.php?ISBN_Libro=<?php echo $ejemplar['ISBN_Libro'] ?>"><?= htmlspecialchars($ejemplar['ISBN_Libro']) ?></a>
                            </td>
                            <td><?= htmlspecialchars($ejemplar['Estado']) ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($ejemplar['Portada']) ?>" alt="Portada"
                                    style="width: 80px; height: auto; display: block;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <br>
        <p>La cantidad de veces que ha accedido a esta página es <?= $numVisitas ?></p>
        <br>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

</body>

</html>