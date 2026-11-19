<?php
//No hace falta iniciar la sesión en este archivo debido a que está iniciada en el index 

//comprobamos que usuario es el que inicia
$usuario = $_SESSION['usuario'];

$opcionesCookie = [
    'expires'  => time() + (7 * 24 * 60 * 60), // Expira en 7 días
    'path'     => '/',                          // Disponible en todo el sitio
    'domain'   => '',                           // Dominio actual
    'secure'   => false,                        // Cambiar a 'true' si usas HTTPS
    'httponly' => true,                         // Evita acceso vía JS (seguridad anti-XSS)
    'samesite' => 'Lax'                         // Protección anti-CSRF
];
$nombreCookie = $usuario;

// 3. Incrementamos el contador
if (isset($_COOKIE[$nombreCookie])) {
    $numVisitas = (int)$_COOKIE[$nombreCookie] + 1;
} else {
    $numVisitas = 1;
}

// 4. Guardamos la cookie actualizada
setcookie($nombreCookie, (string)$numVisitas, $opcionesCookie);