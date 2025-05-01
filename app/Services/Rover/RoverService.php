<?php

namespace App\Services\Rover;

/**
 * Clase RoverService
 *
 * Servicio que gestiona la ejecución de comandos para un rover en una cuadrícula.
 * Actúa como una capa de abstracción entre el controlador y las clases del dominio.
 * Se encarga de crear las instancias necesarias (Rover, Grid, CommandExecutor) y
 * coordinar la ejecución de comandos para el rover.
 */
class RoverService
{
    /**
     * Ejecuta una secuencia de comandos para un rover.
     *
     * @param array $position Posición inicial del rover como array ['x' => int, 'y' => int]
     * @param string $direction Dirección inicial del rover ('N', 'S', 'E', 'W')
     * @param string $commands Cadena de comandos a ejecutar ('F', 'L', 'R')
     * @param array $obstacles Lista de obstáculos como array de posiciones [['x' => int, 'y' => int], ...]
     * @return RoverExecutionResult Objeto que encapsula el estado final del rover
     */
    public function executeCommands(array $position, string $direction, string $commands, array $obstacles = []): RoverExecutionResult
    {
        // Transformar el formato de los obstáculos al formato esperado por Grid
        $obstacles = collect($obstacles)
            ->map(fn($obs) => [$obs['x'], $obs['y']])
            ->toArray();

        // Crear instancias de Rover, Grid y CommandExecutor
        $rover = new Rover($position['x'], $position['y'], $direction);
        $grid = new Grid(200, 200, $obstacles);
        $executor = new CommandExecutor($rover, $grid);

        // Ejecutar los comandos
        $executor->execute($commands);

        // Devolver el estado final del rover
        return RoverExecutionResult::fromRover($rover);
    }
}
