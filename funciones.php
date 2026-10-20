<?php
declare(strict_types=1);

// Nota mínima para aprobar (requerimiento 3)
const NOTA_MINIMA = 70;

/**
 * Valida que una nota sea un número entre $min y $max.
 * Devuelve la nota como float, o null si no es válida.
 */
function validarNota(string $valor, float $min = 0, float $max = 100): ?float
{
    $valor = trim($valor);
    // is_numeric acepta "85" o "85.5", pero no textos como "abc"
    if ($valor === '' || !is_numeric($valor)) {
        return null;
    }
    $nota = (float) $valor;
    return ($nota >= $min && $nota <= $max) ? $nota : null;
}

/**
 * Valida el nombre: no vacío y máximo 60 caracteres.
 */
function validarNombre(string $nombre): bool
{
    $nombre = trim($nombre);
    return $nombre !== '' && mb_strlen($nombre) <= 60;
}

/**
 * Formatea un promedio con la cantidad de decimales indicada
 * (por defecto 1, como pide la tarea).
 */
function formatearPromedio(float $promedio, int $decimales = 1): string
{
    return number_format($promedio, $decimales, '.', ',');
}

/**
 * Escapa texto para mostrarlo de forma segura en HTML.
 */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
