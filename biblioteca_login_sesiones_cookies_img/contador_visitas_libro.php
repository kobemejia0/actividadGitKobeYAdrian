<?php

// Obtenemos el usuario de la sesión y el ISBN seleccionado
$usuario = $_SESSION['usuario'] ?? 'anonimo';
$isbn = $_GET['ISBN_Libro'] ?? null;

$numVisitasLibro = 0;

if ($isbn) {
    // Creamos un nombre de cookie único combinando el usuario y el ISBN
    $nombreCookie = "visitas_libro_" . $usuario . "_" . $isbn;

    $opcionesCookie = [
        'expires'  => time() + (30 * 24 * 60 * 60), // Expira en 30 días
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ];

    // Incrementamos el contador para este libro y usuario específico
    if (isset($_COOKIE[$nombreCookie])) {
        $numVisitasLibro = (int)$_COOKIE[$nombreCookie] + 1;
    } else {
        $numVisitasLibro = 1;
    }

    // Guardamos la cookie actualizada
    setcookie($nombreCookie, (string)$numVisitasLibro, $opcionesCookie);
}
?>