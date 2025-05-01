<?php

namespace App\Services\Rover;

/**
 * Clase RoverExecutionResult
 *
 * Data Transfer Object (DTO) que encapsula el resultado de la ejecución de comandos para un rover.
 * Esta clase proporciona una estructura tipada para los datos de resultado, facilitando su uso en
 * diferentes partes de la aplicación y mejorando la documentación del código.
 */
readonly class RoverExecutionResult
{
    /**
     * Constructor de la clase RoverExecutionResult.
     *
     * @param array $position Posición final del rover como array con las coordenadas 'x' e 'y'.
     * @param string $direction Dirección final del rover ('N', 'S', 'E', 'W').
     * @param bool $obstacleFound Indica si el rover encontró algún obstáculo durante su recorrido.
     * @param bool $boundaryHit Indica si el rover intentó moverse fuera de los límites de la cuadrícula.
     */
    public function __construct(
        public array  $position,
        public string $direction,
        public bool   $obstacleFound,
        public bool   $boundaryHit
    ) {
    }

    /**
     * Método estático de fábrica que crea una instancia de RoverExecutionResult a partir de un objeto Rover.
     *
     * Este método facilita la creación de un resultado de ejecución obteniendo todos los datos
     * necesarios directamente del rover.
     *
     * @param Rover $rover El rover del cual extraer el estado final.
     * @return self Nueva instancia de RoverExecutionResult con los datos del rover.
     */
    public static function fromRover(Rover $rover): self
    {
        return new self(
            position: [
                'x' => $rover->getX(),
                'y' => $rover->getY(),
            ],
            direction: $rover->getDirection(),
            obstacleFound: $rover->hasObstacle(),
            boundaryHit: $rover->hasHitBoundary()
        );
    }

    /**
     * Convierte la instancia actual a un array asociativo.
     *
     * Este método es útil para transformar el DTO en un formato adecuado
     * para respuestas JSON en API o para almacenamiento.
     *
     * @return array Representación del resultado como array asociativo.
     */
    public function toArray(): array
    {
        return [
            'position' => $this->position,
            'direction' => $this->direction,
            'obstacle_found' => $this->obstacleFound,
            'boundary_hit' => $this->boundaryHit,
        ];
    }

    /**
     * Obtiene las coordenadas X e Y de la posición final del rover.
     *
     * @return array Array asociativo con las coordenadas ['x' => int, 'y' => int].
     */
    public function getPosition(): array
    {
        return $this->position;
    }

    /**
     * Obtiene la coordenada X de la posición final del rover.
     *
     * @return int Coordenada X.
     */
    public function getX(): int
    {
        return $this->position['x'];
    }

    /**
     * Obtiene la coordenada Y de la posición final del rover.
     *
     * @return int Coordenada Y.
     */
    public function getY(): int
    {
        return $this->position['y'];
    }

    /**
     * Obtiene la dirección final del rover.
     *
     * @return string Dirección ('N', 'S', 'E', 'W').
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * Verifica si el rover encontró algún obstáculo durante la ejecución de los comandos.
     *
     * @return bool True si se encontró un obstáculo, false en caso contrario.
     */
    public function hasObstacleFound(): bool
    {
        return $this->obstacleFound;
    }

    /**
     * Verifica si el rover intentó moverse fuera de los límites de la cuadrícula.
     *
     * @return bool True si intentó moverse fuera de límites, false en caso contrario.
     */
    public function hasBoundaryHit(): bool
    {
        return $this->boundaryHit;
    }
}
