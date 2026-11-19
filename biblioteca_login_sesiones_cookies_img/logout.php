<?php
session_start();

// Vaciamos el array de sesión
//$_SESSION = array();
foreach (array_keys($_SESSION) as $key) {
    unset($_SESSION[$key]);
}

// Destruimos la sesión
session_destroy();

// Redirigimos al login
header('Location: login.php');
exit;