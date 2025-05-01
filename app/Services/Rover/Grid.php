<?php

namespace App\Services\Rover;

/**
 * Clase Grid
 *
 * Representa una cuadrícula bidimensional que define el entorno donde se mueve el rover.
 * Mantiene las dimensiones del terreno y la ubicación de los obstáculos.
 */
class Grid
{
    /**
     * Ancho de la cuadrícula.
     */
    private int $width;

    /**
     * Altura de la cuadrícula.
     */
    private int $height;

    /**
     * Colección de obstáculos presentes en la cuadrícula.
     *
     * @var Obstacle[]
     */
    private array $obstacles;

    /**
     * Constructor de la clase Grid.
     *
     * @param int $width Ancho de la cuadrícula.
     * @param int $height Altura de la cuadrícula.
     * @param array $obstacles Lista de obstáculos. Puede ser un array de coordenadas [x,y] o instancias de Obstacle.
     * @throws \InvalidArgumentException Si las dimensiones son números negativos.
     */
    public function __construct(int $width, int $height, array $obstacles = [])
    {
        // Validar que las dimensiones sean positivas
        if ($width < 0 || $height < 0) {
            throw new \InvalidArgumentException('Width and Height must be non-negative integers.');
        }

        $this->width = $width;
        $this->height = $height;
        $this->obstacles = [];

        foreach ($obstacles as $obstacle) {
            if (is_array($obstacle) && count($obstacle) === 2) {
                // Solo agregar si está dentro de los límites
                if ($this->isWithinBounds($obstacle[0], $obstacle[1])) {
                    $this->addObstacle(new Obstacle($obstacle[0], $obstacle[1]));
                }
            } elseif ($obstacle instanceof Obstacle) {
                // Verificar si el obstáculo está dentro de los límites
                if ($this->isWithinBounds($obstacle->getX(), $obstacle->getY())) {
                    $this->addObstacle($obstacle);
                }
            }
        }
    }

    /**
     * Obtiene el ancho de la cuadrícula.
     *
     * @return int El ancho de la cuadrícula.
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Obtiene la altura de la cuadrícula.
     *
     * @return int La altura de la cuadrícula.
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Obtiene todos los obstáculos presentes en la cuadrícula.
     *
     * @return Obstacle[] Array de obstáculos.
     */
    public function getObstacles(): array
    {
        return $this->obstacles;
    }

    /**
     * Añade un nuevo obstáculo a la cuadrícula.
     *
     * @param Obstacle $obstacle El obstáculo a añadir.
     */
    public function addObstacle(Obstacle $obstacle): void
    {
        $this->obstacles[] = $obstacle;
    }

    /**
     * Verifica si hay un obstáculo en las coordenadas especificadas.
     *
     * @param int $x Coordenada X a verificar.
     * @param int $y Coordenada Y a verificar.
     * @return bool True si hay un obstáculo en las coordenadas dadas, false en caso contrario.
     */
    public function isObstacle(int $x, int $y): bool
    {
        foreach ($this->obstacles as $obstacle) {
            if ($obstacle->getX() === $x && $obstacle->getY() === $y) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si las coordenadas especificadas están dentro de los límites de la cuadrícula.
     *
     * @param int $x Coordenada X a verificar.
     * @param int $y Coordenada Y a verificar.
     * @return bool True si las coordenadas están dentro de los límites, false en caso contrario.
     */
    public function isWithinBounds(int $x, int $y): bool
    {
        return $x >= 0 && $x < $this->width && $y >= 0 && $y < $this->height;
    }
}
