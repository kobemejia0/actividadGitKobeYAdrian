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
//si no es administrador que vaya al index de cliente
if (!str_contains($_SESSION['usuario'], 'admin')) {
    header('Location: index.php');
    exit;
}
$ejemplares = [];
$error = null;
$libro = null;
try {
    $conexion = conectar("bibliotecario", "bibliotecario");
    //como recomendacion, no usar select * sino cada argumento para poder validarlo
    $consultaSQL = "SELECT e.Id_Ejemplar, e.ISBN_Libro, e.Estado, l.Portada 
                FROM ejemplares e 
                JOIN libros l ON e.ISBN_Libro = l.ISBN";
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

<body class="panel-admin">
    <div class="contenedor-admin">
        <h1>Hola <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
        <h3>Ejemplares disponibles</h3>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (empty($ejemplares)): ?>
            <p> No hay ejemplares disponibles </p>
        <?php else: ?>
            <!-- Formulario para actualizar el estado de varios ejemplares a la vez -->
            <form action="actualizar_estado_ejemplar.php" method="post">
                <!--Creamos una tabla para mostrar los datos-->
                <table>
                    <thead>
                        <tr>
                            <th>ID Ejemplar</th>
                            <th>ISBN libro</th>
                            <th>Estado</th>
                            <th>Portada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ejemplares as $ejemplar): ?>
                            <tr>
                                <td><?= htmlspecialchars($ejemplar['Id_Ejemplar']) ?></td>
                                <td>
                                    <a
                                        href="libro.php?ISBN_Libro=<?php echo $ejemplar['ISBN_Libro'] ?>"><?= htmlspecialchars($ejemplar['ISBN_Libro']) ?></a>
                                </td>
                                <td>
                                    <!-- si solo se quiere imprimir en php se usa < ? = en vez de < ? php echo  -->
                                    <!-- Creamos un array con los ejemplares -->
                                    <select name="estados[<?= $ejemplar['Id_Ejemplar'] ?>]" id="">
                                        <option value="Disponible" <?= $ejemplar['Estado'] === 'Disponible' ? 'selected' : '' ?>>
                                            Disponible</option>
                                        <option value="Prestado" <?= $ejemplar['Estado'] === 'Prestado' ? 'selected' : '' ?>>
                                            Prestado</option>
                                        <option value="Dado de baja" <?= $ejemplar['Estado'] === 'Dado de baja' ? 'selected' : '' ?>>Dado de baja</option>
                                    </select>
                                </td>
                                <td>
                                    <img src="<?= htmlspecialchars($ejemplar['Portada'] ?? 'img/default.jpg') ?>" alt="Portada"
                                    style="width: 80px; height: auto; display: block;">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Guardar cambios</button>
            </form>

        <?php endif; ?>
        <br>
        <p>La cantidad de veces que ha accedido a esta página es <?= $numVisitas ?></p>
        <br>
        <a href="altaLibro.php">Dar de alta a los libros</a>
        <br>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

</body>

</html>