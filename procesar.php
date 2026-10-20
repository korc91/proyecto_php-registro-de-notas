<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/Estudiante.php';

// Solo se aceptan datos enviados por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Lee los datos del formulario ($_POST); ?? evita avisos si falta un campo
$nombre = trim((string) ($_POST['nombre'] ?? ''));
$textos = [
    (string) ($_POST['nota1'] ?? ''),
    (string) ($_POST['nota2'] ?? ''),
    (string) ($_POST['nota3'] ?? ''),
];

$errores = [];
$notas   = [];

// Validación en el servidor
if (!validarNombre($nombre)) {
    $errores[] = 'El nombre es obligatorio (máximo 60 caracteres).';
}
foreach ($textos as $i => $texto) {
    $nota = validarNota($texto);
    if ($nota === null) {
        $errores[] = 'La nota ' . ($i + 1) . ' debe ser un número entre 0 y 100.';
    } else {
        $notas[] = $nota;
    }
}

if (count($errores) > 0) {
    // Guarda los errores y lo que escribió el usuario para mostrarlos en index.php
    $_SESSION['errores'] = $errores;
    $_SESSION['viejo']   = ['nombre' => $nombre, 'notas' => $textos];
} else {
    // Crea el estudiante y guarda en la sesión solo un arreglo (no el objeto)
    $estudiante = new Estudiante($nombre, $notas);
    $_SESSION['estudiantes'][] = $estudiante->aArreglo();
    $_SESSION['exito'] = 'Se agregó a ' . $estudiante->getNombre() . ' con promedio '
        . formatearPromedio($estudiante->promedio()) . '.';
}

// Patrón POST/Redirect/GET: al recargar no se agrega dos veces
header('Location: index.php');
exit;
