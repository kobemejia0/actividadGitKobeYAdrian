<?php
/*
libros a insertar de prueba:
978-84-444-4444-4
La Galatea
Primera novela de Miguel de Cervantes

978-84-555-5555-5
El Hobbit
Narra la aventura del Hobbit Bilbo Bolsón junto al...

978-84-666-6666-6
Yo, robot
Tres leyes de la robotica
*/
//inciamos la sesion para solo poder acceder a esta página si se han loggeado
session_start();
//Incluimos la conexión
require_once 'conexion.php';

//Para evitar accesos indeseados comprobamos que exista la sesion
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
//si no es administrador que vaya al index de cliente
if (!str_contains($_SESSION['usuario'], 'admin')) {
    header('Location: index.php');
    exit;
}
//variable que te lleva al index correcto
$urlCorrecta = str_contains($_SESSION['usuario'] ?? '', 'admin') ? 'index_admin.php' : 'index.php';

//variables para sus correspondientes mensajes
$error = null;
$mensaje = null;

try {
    //conexion
    $conexion = conectar("bibliotecario", "bibliotecario");
    //autores
    $consultaAutor = "SELECT Id, Nombre, Apellidos from autores";
    $statement = $conexion->query($consultaAutor);
    $autores = $statement->fetchAll();
    //editoriales
    $consultaEditorial = "SELECT Id, Nombre, Pais from editoriales";
    $statement = $conexion->query($consultaEditorial);
    $editoriales = $statement->fetchAll();

    //si el método es el correcto y el array tiene valores validos
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['libros'])){
        //beginTransaction se utiliza para enviar iniciar una transacción y desactivar la confirmación automatica
        //Esto hace que se ejecuten todas las consultas como una y en caso de error (en cualquiera de las consultas) que no se realicen los cambios
        $conexion->beginTransaction();
        //Consulta sql para insertar libros 
        $consultaInsert = "INSERT INTO libros (ISBN, Titulo, Sinopsis, Id_Autor, Id_Editorial, Portada) VALUES (?,?,?,?,?,?)";

        //consulta para insertar esos libros en la tabla ejemplares para verlos en el inicio
        $consultaInsertEjemplar = "INSERT INTO ejemplares (ISBN_Libro, Estado) VALUES (?, ?)";
        $statementEjemplar = $conexion->prepare($consultaInsertEjemplar);
        //Preparamos la consulta
        $statementInsert = $conexion->prepare($consultaInsert);
        //Recorremos cada libro para poder añadirlos
        foreach ($_POST['libros'] as $libro) {
            //comprobamos que al menos el isbn, el titulo, el autor y la editorial no estén vacios
            if(!empty($libro['isbn']) && !empty($libro['titulo']) && !empty($libro['autores']) && !empty($libro['editoriales'])){
                $statementInsert->execute([
                    //tal y como se llaman en el formulario
                    $libro['isbn'],
                    $libro['titulo'],
                    $libro['sinopsis'],
                    $libro['autores'],
                    $libro['editoriales'],
                    $libro['portada'],
                ]);
                // creamos su ejemplar automáticamente
                $statementEjemplar->execute([
                    $libro['isbn'],
                    //Al añadirlos por defecto están disponibles
                    'Disponible' 
                ]);
            }
        }
        //Confirmamos los cambios en la base de datos
        $conexion->commit();
        //Aplicamos un mensaje para confirmar los cambios
        $mensaje = "Libros insertados correctamente";


    }
} catch (PDOException $e) {
        $error = $e->getMessage();
        //comprobamos que exista la conexión antes de deshacer la consulta
        //Si se ha hecho un begintransaction (lo que quiere decir que tenemos la consulta abierta)
        if(isset($conexion) && $conexion->inTransaction()){
            //revertimos los cambios de la consulta 
            $conexion->rollBack();
        }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>alta libros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Alta de libros</h1>
    <form action="" method="post">
        <?php for($i = 1; $i<4; $i++): ?>
        <fieldset>
            <!-- Agrupamos los libros según el indice para un mejor manejo
            Se crea un array bidimensional por cada libro donde su contenido va asociado al indice
            Lo que permite que no se mezcle informacion de cada libro -->
            <legend>Libro <?= $i ?></legend>
            <label for="">ISBN:</label>
            <input type="text" name="libros[<?= $i ?>][isbn]" required>
            <br>
            <label for="">Titulo:</label>
            <input type="text" name="libros[<?= $i ?>][titulo]" required>
            <br>
            <label for="">Sinopsis:</label>
            <textarea name="libros[<?= $i ?>][sinopsis]" id=""></textarea>
            <br>
            <label for="">Portada (ruta):</label>
            <input type="text" name="libros[<?= $i ?>][portada]" placeholder="img/imagen.jpg">
            <br>
            <!-- Solo autores y editoriales disponibles -->
            <label for="">Autor:</label>
            <select name="libros[<?= $i ?>][autores]" id="" required>
                <option value="">Selecciona un autor</option>
                <?php foreach($autores as $autor): ?>
                <option value="<?= $autor['Id'] ?>" ><?= $autor['Nombre'] . " " . $autor['Apellidos'] ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="">Editorial:</label>
            <select name="libros[<?= $i ?>][editoriales]" id="" required>
                <option value="">Selecciona una editorial</option>
                <?php foreach($editoriales as $editorial): ?>
                <option value="<?= $editorial['Id'] ?>" ><?= $editorial['Nombre'] . " " . $editorial['Pais'] ?></option>
                <?php endforeach; ?>
            </select>
        </fieldset>
        <?php endfor; ?>
        <button type="submit">Enviar</button>
    </form>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <p style="color: green;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    <a href="<?= $urlCorrecta ?>">Volver</a>

</body>
</html>