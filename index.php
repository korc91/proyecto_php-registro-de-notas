<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/Estudiante.php';

// Mensajes de la petición anterior (se muestran una sola vez y se borran)
$errores = $_SESSION['errores'] ?? [];
$exito   = $_SESSION['exito'] ?? '';
$viejo   = $_SESSION['viejo'] ?? ['nombre' => '', 'notas' => ['', '', '']];
unset($_SESSION['errores'], $_SESSION['exito'], $_SESSION['viejo']);

// Filtro recibido por $_GET; si no es válido se usa "todos"
$filtrosValidos = ['todos', 'aprobados', 'reprobados'];
$filtro = (string) ($_GET['filtro'] ?? 'todos');
if (!in_array($filtro, $filtrosValidos, true)) {
    $filtro = 'todos';
}

// Reconstruye los objetos Estudiante a partir de los arreglos de la sesión
$todos = [];
foreach ($_SESSION['estudiantes'] ?? [] as $datos) {
    $todos[] = new Estudiante($datos['nombre'], $datos['notas']);
}

// Aplica el filtro
$lista = array_filter($todos, function (Estudiante $est) use ($filtro): bool {
    return match ($filtro) {
        'aprobados'  => $est->estado() === 'Aprobado',
        'reprobados' => $est->estado() === 'Reprobado',
        default      => true,
    };
});

// Reto opcional: promedio general y mejor estudiante (de todo el grupo)
$promedioGeneral = 0.0;
$mejor = null;
if (count($todos) > 0) {
    $suma = 0.0;
    foreach ($todos as $est) {
        $suma += $est->promedio();
        if ($mejor === null || $est->promedio() > $mejor->promedio()) {
            $mejor = $est;
        }
    }
    $promedioGeneral = $suma / count($todos);
}

require __DIR__ . '/encabezado.php';
?>

<section class="tarjeta">
    <h2>Agregar estudiante</h2>

    <?php if (count($errores) > 0): ?>
        <div class="alerta alerta-error">
            <strong>No se agregó el estudiante:</strong>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($exito !== ''): ?>
        <div class="alerta alerta-exito"><?= e($exito) ?></div>
    <?php endif; ?>

    <!-- El formulario envía los datos a procesar.php por POST -->
    <form action="procesar.php" method="post" class="formulario" novalidate>
        <div class="campo campo-nombre">
            <label for="nombre">Nombre del estudiante</label>
            <input type="text" id="nombre" name="nombre" maxlength="60"
                   value="<?= e($viejo['nombre']) ?>" placeholder="Ej.: Ana Rodríguez">
        </div>
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="campo">
                <label for="nota<?= $i ?>">Nota <?= $i ?></label>
                <input type="number" id="nota<?= $i ?>" name="nota<?= $i ?>" min="0" max="100" step="0.01"
                       value="<?= e((string) ($viejo['notas'][$i - 1] ?? '')) ?>" placeholder="0 – 100">
            </div>
        <?php endfor; ?>
        <button type="submit" class="boton">Agregar</button>
    </form>
</section>

<section class="tarjeta">
    <div class="barra">
        <h2>Estudiantes</h2>
        <!-- Los filtros envían su valor por GET en la URL -->
        <nav class="filtros">
            <?php foreach ($filtrosValidos as $f): ?>
                <a href="index.php?filtro=<?= $f ?>" class="<?= $f === $filtro ? 'activo' : '' ?>">
                    <?= ucfirst($f) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <?php if (count($lista) === 0): ?>
        <p class="vacio">
            <?= count($todos) === 0 ? 'Todavía no hay estudiantes registrados.' : 'Ningún estudiante coincide con este filtro.' ?>
        </p>
    <?php else: ?>
        <table class="tabla">
            <thead>
            <tr>
                <th>Nombre</th><th>Nota 1</th><th>Nota 2</th><th>Nota 3</th><th>Promedio</th><th>Estado</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($lista as $est): ?>
                <tr>
                    <td><?= e($est->getNombre()) ?></td>
                    <?php foreach ($est->getNotas() as $nota): ?>
                        <td class="num"><?= formatearPromedio($nota) ?></td>
                    <?php endforeach; ?>
                    <td class="num"><strong><?= formatearPromedio($est->promedio()) ?></strong></td>
                    <td>
                        <span class="etiqueta <?= $est->estado() === 'Aprobado' ? 'ok' : 'mal' ?>">
                            <?= $est->estado() ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">Promedio general del grupo (<?= count($todos) ?> estudiantes)</td>
                <td class="num"><strong><?= formatearPromedio($promedioGeneral) ?></strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4">Mejor promedio</td>
                <td class="num"><strong><?= formatearPromedio($mejor->promedio()) ?></strong></td>
                <td><?= e($mejor->getNombre()) ?></td>
            </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <?php if (count($todos) > 0): ?>
        <!-- Borrar va por POST para que no se active solo con abrir un enlace -->
        <form action="borrar.php" method="post" class="form-borrar">
            <button type="submit" class="boton boton-peligro">Borrar lista</button>
        </form>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/pie.php'; ?>
