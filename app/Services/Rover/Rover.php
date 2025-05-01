<?php

namespace App\Services\Rover;

/**
 * Clase Rover
 *
 * Representa un vehículo explorador (rover) capaz de moverse por una cuadrícula.
 * Mantiene información sobre su posición actual, dirección y estado con respecto
 * a obstáculos o límites encontrados durante su movimiento.
 */
class Rover
{
    /**
     * Coordenada X actual del rover en la cuadrícula.
     */
    private int $x;

    /**
     * Coordenada Y actual del rover en la cuadrícula.
     */
    private int $y;

    /**
     * Dirección actual del rover (N: Norte, S: Sur, E: Este, W: Oeste).
     */
    private string $direction;

    /**
     * Indica si el rover ha encontrado un obstáculo durante su recorrido.
     */
    private bool $obstacleFound = false;

    /**
     * Indica si el rover ha intentado moverse fuera de los límites de la cuadrícula.
     */
    private bool $hitBoundary = false;

    /**
     * Constructor de la clase Rover.
     *
     * @param int $x Coordenada X inicial del rover.
     * @param int $y Coordenada Y inicial del rover.
     * @param string $direction Dirección inicial del rover ('N', 'S', 'E', 'W').
     * @throws \InvalidArgumentException Si la dirección proporcionada no es válida.
     */
    public function __construct(int $x, int $y, string $direction)
    {
        // Validar la dirección inicial
        if (!in_array($direction, ['N', 'S', 'E', 'W'])) {
            throw new \InvalidArgumentException("Invalid direction: $direction");
        }

        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
    }

    /**
     * Mueve el rover una unidad hacia adelante en su dirección actual.
     */
    public function moveForward(): void
    {
        match ($this->direction) {
            'N' => $this->y++,
            'S' => $this->y--,
            'E' => $this->x++,
            'W' => $this->x--,
        };
    }

    /**
     * Gira el rover 90 grados a la izquierda (sentido antihorario).
     */
    public function turnLeft(): void
    {
        $this->direction = match ($this->direction) {
            'N' => 'W',
            'W' => 'S',
            'S' => 'E',
            'E' => 'N',
        };
    }

    /**
     * Gira el rover 90 grados a la derecha (sentido horario).
     */
    public function turnRight(): void
    {
        $this->direction = match ($this->direction) {
            'N' => 'E',
            'E' => 'S',
            'S' => 'W',
            'W' => 'N',
        };
    }

    /**
     * Obtiene la posición actual del rover.
     *
     * @return array Posición actual del rover como un array con las claves 'x', 'y' y 'direction'.
     */
    public function getPosition(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'direction' => $this->direction,
        ];
    }

    /**
     * Obtiene la coordenada X actual del rover.
     *
     * @return int Coordenada X actual.
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Obtiene la coordenada Y actual del rover.
     *
     * @return int Coordenada Y actual.
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Obtiene la dirección actual del rover.
     *
     * @return string Dirección actual ('N', 'S', 'E', 'W').
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * Marca que el rover ha encontrado un obstáculo.
     */
    public function markObstacleFound(): void
    {
        $this->obstacleFound = true;
    }

    /**
     * Verifica si el rover ha encontrado algún obstáculo.
     *
     * @return bool True si el rover ha encontrado un obstáculo, false en caso contrario.
     */
    public function hasObstacle(): bool
    {
        return $this->obstacleFound;
    }

    /**
     * Marca que el rover ha intentado moverse fuera de los límites de la cuadrícula.
     */
    public function markHitBoundary(): void
    {
        $this->hitBoundary = true;
    }

    /**
     * Verifica si el rover ha intentado moverse fuera de los límites.
     *
     * @return bool True si el rover ha intentado moverse fuera de los límites, false en caso contrario.
     */
    public function hasHitBoundary(): bool
    {
        return $this->hitBoundary;
    }
}
