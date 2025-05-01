<?php

namespace App\Services\Rover;

/**
 * Clase CommandExecutor
 *
 * Esta clase es responsable de ejecutar comandos para mover y controlar el rover en un entorno definido por una cuadrícula (Grid).
 * Gestiona las acciones del rover, incluyendo movimientos, giros y detección de obstáculos o límites del entorno.
 */
class CommandExecutor
{
    private Rover $rover;
    private Grid $grid;

    /**
     * Constructor de la clase CommandExecutor
     *
     * @param Rover $rover Instancia del rover que se desea controlar.
     * @param Grid $grid Instancia de la cuadrícula que define el entorno del rover.
     */
    public function __construct(Rover $rover, Grid $grid)
    {
        $this->rover = $rover;
        $this->grid = $grid;
    }

    /**
     * Ejecuta una secuencia de comandos para controlar el rover.
     *
     * @param string $commands Cadena de comandos ("F" para avanzar, "L" para girar a la izquierda, "R" para girar a la derecha).
     * @return array Estado del rover después de ejecutar los comandos.
     */
    public function execute(string $commands): array
    {
        // Comprobar si la posición inicial está bloqueada por un obstáculo
        if ($this->grid->isObstacle($this->rover->getX(), $this->rover->getY())) {
            $this->rover->markObstacleFound();
            return $this->getState();
        }

        // Procesar los comandos uno por uno
        foreach (str_split($commands) as $command) {
            if ($command === 'F') {
                $this->checkObstacleAndMove();
            } else {
                match ($command) {
                    'L' => $this->rover->turnLeft(),
                    'R' => $this->rover->turnRight(),
                };
            }
        }

        return $this->getState();
    }

    /**
     * Verifica si existe algún obstáculo en la posición futura y realiza el movimiento del rover si no hay obstáculos.
     */
    private function checkObstacleAndMove(): void
    {
        // Calcular la posición futura en base a la dirección
        [$futureX, $futureY] = $this->getNextPosition();

        // Verificar si el rover está fuera de los límites
        if (!$this->grid->isWithinBounds($futureX, $futureY)) {
            $this->rover->markHitBoundary();
            return;
        }

        // Verificar si la posición futura tiene algún obstáculo
        if ($this->grid->isObstacle($futureX, $futureY)) {
            $this->rover->markObstacleFound();
            return;
        }

        // Mover el rover hacia adelante
        $this->rover->moveForward();
    }

    /**
     * Calcula la próxima posición del rover en base a su posición actual y dirección.
     *
     * @return array Coordenadas (x, y) de la próxima posición.
     */
    private function getNextPosition(): array
    {
        $futureX = $this->rover->getX();
        $futureY = $this->rover->getY();

        match ($this->rover->getDirection()) {
            'N' => $futureY++,
            'S' => $futureY--,
            'E' => $futureX++,
            'W' => $futureX--,
        };

        return [$futureX, $futureY];
    }

    /**
     * Devuelve el estado actual del rover, incluyendo posición, dirección y banderas de estado.
     *
     * @return array Estado actual del rover.
     */
    private function getState(): array
    {
        return [
            'position' => [
                'x' => $this->rover->getX(),
                'y' => $this->rover->getY(),
            ],
            'direction' => $this->rover->getDirection(),
            'obstacle_found' => $this->rover->hasObstacle(),
            'boundary_hit' => $this->rover->hasHitBoundary(),
        ];
    }
}
