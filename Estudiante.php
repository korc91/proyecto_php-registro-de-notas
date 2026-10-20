<?php
declare(strict_types=1);

class Estudiante
{
    private string $nombre;
    /** @var float[] */
    private array $notas;

    public function __construct(string $nombre, array $notas)
    {
        // Guarda el nombre sin espacios sobrantes y las notas como float
        $this->nombre = trim($nombre);
        $this->notas  = array_map('floatval', $notas);
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getNotas(): array
    {
        return $this->notas;
    }

    // Suma las notas y divide entre la cantidad
    public function promedio(): float
    {
        if (count($this->notas) === 0) {
            return 0.0;
        }
        return array_sum($this->notas) / count($this->notas);
    }

    // Devuelve "Aprobado" o "Reprobado" usando la constante NOTA_MINIMA
    public function estado(): string
    {
        return $this->promedio() >= NOTA_MINIMA ? 'Aprobado' : 'Reprobado';
    }

    // Convierte el objeto en arreglo para guardarlo en $_SESSION
    public function aArreglo(): array
    {
        return ['nombre' => $this->nombre, 'notas' => $this->notas];
    }
}
