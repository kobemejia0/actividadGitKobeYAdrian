<?php
//Iniciamos la sesion para poder trabajar con ella
session_start();
//Establecemos la conexión con la base de datos:
require_once 'conexion.php';

// Si el usuario ya está autenticado, lo mandamos directamente al index
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$error = null;
//comprobamos que se recibe por el método correcto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Guardamos los datos recibidos por el formulario
    $nombreUsuario = $_POST['nombre_usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    //Comprobamos que el usuario y la contraseña no estén vacios
    if (!empty($nombreUsuario) && !empty($contraseña)) {
        try {
            //Establecemos la conexión
            $conexion = conectar();

            //Consulta sql donde comprabamos solo que existe el usuario en la bd
            $consultaSQL = "SELECT Nombre_Usuario, Contrasena FROM usuarios where Nombre_Usuario = :nombreUsuario";
            //preparamos la consulta
            $statement = $conexion->prepare($consultaSQL);
            //La ejecutamos pasando el parametro
            $statement->execute([":nombreUsuario" => $nombreUsuario]);
            //Obtenemos el registro que nos devuelve un array si existe o false
            //obtenemos ese registro como un array asociativo
            $usuario = $statement->fetch(PDO::FETCH_ASSOC);

            //Comprobamos que el usuario existe y que coincide con la contraseña
            //si usuario tiene contenido es true, y luego comparamos al ser un array asoc su clave y el valor introducido
            if ($usuario && $usuario["Contrasena"] === $contraseña && str_contains($usuario['Nombre_Usuario'], "admin")) {
                //si el usuario que inicia es admin cambiamos en la base de datos al inicio como bibliotecario
                $conexionAdmin = conectar('bibliotecario', 'bibliotecario');
                //si los datos son correctos guardamos la sesión
                $_SESSION['usuario'] = $usuario['Nombre_Usuario'];
                header('Location: index_admin.php');
                exit;

            } else if ($usuario && $usuario["Contrasena"] == $contraseña) {
                //dejamos la conexion como cliente
                $conexionAdmin = conectar('cliente', 'cliente');
                //si los datos son correctos guardamos la sesión
                $_SESSION['usuario'] = $usuario['Nombre_Usuario'];
                // Redirigimos al index.php
                header('Location: index.php');
                exit; // Siempre detener la ejecución después de header()

            } else {
                $error = "Usuario o contraseña no válidos";
            }

        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();

        }

    } else {
        $error = "faltan datos";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login">
    <div class="login-contenido">
        <h3>Cuenta de usuario</h3>
        <?php if ($error != null): ?>
            <p>
                <?php echo htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>
        <form action="login.php" method="post" class="formulario">
            <label for="">Introduce tu nombre de usuario:</label>
            <input type="text" name="nombre_usuario" placeholder="Nombre de usuario">
            <label for="">Introduce tu contraseña:</label>
            <input type="password" name="contraseña" placeholder="Contraseña">
            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>