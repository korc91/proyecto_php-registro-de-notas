<?php
declare(strict_types=1);

session_start();

// Solo se borra si la petición viene del botón (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION = [];          // vacía todas las variables de sesión
    session_destroy();       // elimina la sesión en el servidor
}

header('Location: index.php');
exit;
