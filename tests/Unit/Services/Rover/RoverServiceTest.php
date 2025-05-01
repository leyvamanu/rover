<?php

namespace Tests\Unit\Services\Rover;

use App\Services\Rover\RoverService;
use PHPUnit\Framework\TestCase;

class RoverServiceTest extends TestCase
{
    private RoverService $roverService;

    /**
     * Configura el entorno de prueba.
     * Se ejecuta antes de cada método de prueba para garantizar un estado limpio.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->roverService = new RoverService();
    }

    /**
     * Verifica que el servicio pueda ejecutar comandos sin obstáculos.
     * El rover debe moverse según los comandos proporcionados sin encontrar impedimentos.
     */
    public function test_execute_commands_without_obstacles()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 0, 'y' => 0],  // Posición inicial
            'N',                   // Dirección inicial
            'FFRFF'                // Comandos: adelante, adelante, derecha, adelante, adelante
        );

        $this->assertEquals([
            'position' => ['x' => 2, 'y' => 2],  // Posición final esperada
            'direction' => 'E',                  // Dirección final esperada
            'obstacle_found' => false,           // No se encontraron obstáculos
            'boundary_hit' => false              // No se alcanzaron límites
        ], $result->toArray());
    }

    /**
     * Verifica que el servicio detecte obstáculos durante la ejecución de comandos.
     * El rover debe detenerse antes del obstáculo y reportar que ha encontrado uno.
     */
    public function test_execute_commands_with_obstacles()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 0, 'y' => 0],    // Posición inicial
            'N',                     // Dirección inicial
            'FFRFF',                 // Comandos a ejecutar
            [['x' => 0, 'y' => 1]]   // Obstáculo en posición (0,1)
        );

        $this->assertEquals([
            'position' => ['x' => 2, 'y' => 0],  // Posición final esperada
            'direction' => 'E',                  // Dirección final esperada
            'obstacle_found' => true,            // Se encontró un obstáculo
            'boundary_hit' => false              // No se alcanzaron límites
        ], $result->toArray());
    }

    /**
     * Verifica que el servicio detecte cuando el rover alcanza un límite.
     * El rover debe detenerse en el límite y reportar que ha alcanzado uno.
     */
    public function test_execute_commands_hitting_boundary()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 0, 'y' => 0],  // Posición inicial en el borde del grid
            'S',                   // Dirección inicial hacia el sur
            'F'                    // Comando para avanzar (que llevaría fuera del límite)
        );

        $this->assertEquals([
            'position' => ['x' => 0, 'y' => 0],  // Posición sin cambios
            'direction' => 'S',                  // Dirección sin cambios
            'obstacle_found' => false,           // No se encontraron obstáculos
            'boundary_hit' => true               // Se alcanzó un límite
        ], $result->toArray());
    }

    /**
     * Verifica que el servicio pueda manejar secuencias complejas de movimientos.
     * El rover debe ejecutar correctamente una serie de comandos que incluyen giros y avances.
     */
    public function test_execute_commands_with_complex_movement()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 0, 'y' => 0],  // Posición inicial
            'N',                   // Dirección inicial
            'FFRFFLFF'             // Secuencia compleja: adelante, adelante, derecha, adelante, adelante, izquierda, adelante, adelante
        );

        $this->assertEquals([
            'position' => ['x' => 2, 'y' => 4],  // Posición final esperada
            'direction' => 'N',                  // Dirección final esperada
            'obstacle_found' => false,           // No se encontraron obstáculos
            'boundary_hit' => false              // No se alcanzaron límites
        ], $result->toArray());
    }

    /**
     * Verifica que el servicio maneje correctamente múltiples obstáculos.
     * El rover debe detenerse al encontrar cualquiera de los obstáculos definidos.
     */
    public function test_execute_commands_with_multiple_obstacles()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 0, 'y' => 0],      // Posición inicial
            'N',                       // Dirección inicial
            'FFRFF',                   // Comandos a ejecutar
            [
                ['x' => 0, 'y' => 1],  // Primer obstáculo
                ['x' => 1, 'y' => 2]   // Segundo obstáculo
            ]
        );

        $this->assertEquals([
            'position' => ['x' => 2, 'y' => 0],  // Posición final esperada
            'direction' => 'E',                  // Dirección final esperada
            'obstacle_found' => true,            // Se encontró un obstáculo
            'boundary_hit' => false              // No se alcanzaron límites
        ], $result->toArray());
    }

    /**
     * Verifica que el rover inicie en una posición con obstáculo.
     * El rover debe detectar el obstáculo inmediatamente y no ejecutar ningún comando.
     */
    public function test_rover_starts_on_obstacle_position()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 1, 'y' => 1],     // Posición inicial
            'N',                      // Dirección inicial
            'FFRFF',                  // Comandos a ejecutar (que no deberían ejecutarse)
            [['x' => 1, 'y' => 1]]    // Obstáculo en la posición inicial del rover
        );

        $this->assertEquals([
            'position' => ['x' => 1, 'y' => 1],  // Posición sin cambios
            'direction' => 'N',                  // Dirección sin cambios
            'obstacle_found' => true,            // Se detectó un obstáculo en la posición inicial
            'boundary_hit' => false              // No se alcanzaron límites
        ], $result->toArray());
    }

    /**
     * Verifica el movimiento en círculo completo del rover.
     * El rover debe regresar a su posición original tras un patrón circular.
     */
    public function test_execute_commands_with_full_circle_movement()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 5, 'y' => 5],  // Posición inicial centrada
            'N',                   // Dirección inicial
            'FRFRFRFR'             // Movimiento en cuadrado: adelante, derecha (4 veces)
        );

        $this->assertEquals([
            'position' => ['x' => 5, 'y' => 5],  // Debe volver a la posición original
            'direction' => 'N',                  // Y a la dirección original
            'obstacle_found' => false,
            'boundary_hit' => false
        ], $result->toArray());
    }

    /**
     * Verifica la rotación completa del rover sin moverse.
     * El rover debe girar 360 grados y mantener su posición original.
     */
    public function test_execute_commands_with_full_rotation()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 3, 'y' => 3],  // Posición inicial
            'N',                   // Dirección inicial
            'RRRR'                 // Cuatro giros a la derecha (360 grados)
        );

        $this->assertEquals([
            'position' => ['x' => 3, 'y' => 3],  // Posición sin cambios
            'direction' => 'N',                  // Debe volver a la dirección original
            'obstacle_found' => false,
            'boundary_hit' => false
        ], $result->toArray());

        $result = $this->roverService->executeCommands(
            ['x' => 3, 'y' => 3],  // Posición inicial
            'N',                   // Dirección inicial
            'LLLL'                 // Cuatro giros a la izquierda (360 grados)
        );

        $this->assertEquals([
            'position' => ['x' => 3, 'y' => 3],  // Posición sin cambios
            'direction' => 'N',                  // Debe volver a la dirección original
            'obstacle_found' => false,
            'boundary_hit' => false
        ], $result->toArray());
    }

    /**
     * Verifica que el rover se detenga cuando encuentra un obstáculo en medio de una secuencia de comandos.
     * El rover debe ejecutar solo los comandos hasta encontrar el obstáculo.
     */
    public function test_stop_at_obstacle_mid_commands()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 0, 'y' => 0],   // Posición inicial
            'E',                    // Dirección inicial (Este)
            'FFF',                  // Comandos: avanzar tres veces
            [['x' => 2, 'y' => 0]]  // Obstáculo en (2,0)
        );

        $this->assertEquals([
            'position' => ['x' => 1, 'y' => 0],  // Debe detenerse antes del obstáculo
            'direction' => 'E',
            'obstacle_found' => true,            // Debe detectar el obstáculo
            'boundary_hit' => false
        ], $result->toArray());
    }

    /**
     * Verifica que el servicio maneje correctamente comandos vacíos.
     * El rover no debe moverse y debe mantener su estado inicial.
     */
    public function test_execute_empty_commands()
    {
        $result = $this->roverService->executeCommands(
            ['x' => 5, 'y' => 5],  // Posición inicial
            'N',                   // Dirección inicial
            ''                     // Sin comandos
        );

        $this->assertEquals([
            'position' => ['x' => 5, 'y' => 5],  // Posición sin cambios
            'direction' => 'N',                  // Dirección sin cambios
            'obstacle_found' => false,
            'boundary_hit' => false
        ], $result->toArray());
    }
}
