<?php

namespace App\Services\Rover;

/**
 * Clase Obstacle
 *
 * Representa un obstáculo en el entorno del rover.
 * Cada obstáculo está definido por sus coordenadas X e Y en la cuadrícula.
 * Esta clase simple permite encapsular la información de posición de los obstáculos
 * y proporciona métodos para acceder a sus coordenadas.
 */
class Obstacle
{
    /**
     * Coordenada X del obstáculo en la cuadrícula.
     */
    private int $x;

    /**
     * Coordenada Y del obstáculo en la cuadrícula.
     */
    private int $y;

    /**
     * Constructor de la clase Obstacle.
     *
     * @param int $x Coordenada X del obstáculo.
     * @param int $y Coordenada Y del obstáculo.
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Obtiene la coordenada X del obstáculo.
     *
     * @return int La coordenada X.
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Obtiene la coordenada Y del obstáculo.
     *
     * @return int La coordenada Y.
     */
    public function getY(): int
    {
        return $this->y;
    }
}
