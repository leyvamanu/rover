<?php

namespace Tests\Unit\Services\Rover;

use App\Services\Rover\CommandExecutor;
use App\Services\Rover\Grid;
use App\Services\Rover\Rover;
use PHPUnit\Framework\TestCase;

class CommandExecutorTest extends TestCase
{
    /**
     * Verifica que el comando para mover adelante (F) funciona correctamente.
     * El rover debe avanzar una posición en la dirección a la que está mirando (Norte).
     */
    public function test_execute_forward_command()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        $executor->execute('F');

        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(1, $rover->getY());
    }

    /**
     * Verifica que el comando para girar a la izquierda (L) cambia correctamente la dirección.
     * El rover debe girar 90 grados a la izquierda, cambiando de Norte (N) a Oeste (W).
     */
    public function test_execute_left_command()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        $executor->execute('L');

        $this->assertEquals('W', $rover->getDirection());
    }

    /**
     * Verifica que el comando para girar a la derecha (R) cambia correctamente la dirección.
     * El rover debe girar 90 grados a la derecha, cambiando de Norte (N) a Este (E).
     */
    public function test_execute_right_command()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        $executor->execute('R');

        $this->assertEquals('E', $rover->getDirection());
    }

    /**
     * Verifica que se pueden ejecutar múltiples comandos secuencialmente.
     * El rover debe moverse y cambiar de dirección según la secuencia de comandos dada.
     */
    public function test_execute_multiple_commands()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        $executor->execute('FFRFF');

        $this->assertEquals(2, $rover->getX());
        $this->assertEquals(2, $rover->getY());
        $this->assertEquals('E', $rover->getDirection());
    }

    /**
     * Verifica que el rover se detiene al encontrar un obstáculo.
     * El rover no debe moverse y debe marcar que ha encontrado un obstáculo.
     */
    public function test_stop_at_obstacle()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10, [[0, 1]]);
        $executor = new CommandExecutor($rover, $grid);

        $executor->execute('F');

        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(0, $rover->getY());
        $this->assertTrue($rover->hasObstacle());
    }

    /**
     * Verifica que el rover se detiene al alcanzar el límite del grid.
     * El rover no debe moverse y debe marcar que ha alcanzado un límite.
     */
    public function test_stop_at_boundary()
    {
        $rover = new Rover(0, 0, 'S');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        $executor->execute('F');

        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(0, $rover->getY());
        $this->assertTrue($rover->hasHitBoundary());
    }

    /**
     * Verifica que el rover se detiene cuando ya está en una posición con un obstáculo.
     * El rover no debe ejecutar ningún comando y debe marcar que ha encontrado un obstáculo.
     */
    public function test_rover_starts_on_obstacle()
    {
        $rover = new Rover(1, 1, 'N');
        $grid = new Grid(10, 10, [[1, 1]]);
        $executor = new CommandExecutor($rover, $grid);

        $result = $executor->execute('FFRFF');

        $this->assertEquals(1, $rover->getX());
        $this->assertEquals(1, $rover->getY());
        $this->assertEquals('N', $rover->getDirection());
        $this->assertTrue($rover->hasObstacle());
        $this->assertEquals(true, $result['obstacle_found']);
    }

    /**
     * Verifica que el rover se mueve correctamente en todas las direcciones.
     * Se prueba el movimiento en las cuatro direcciones (N, S, E, W).
     */
    public function test_move_in_all_directions()
    {
        $rover = new Rover(5, 5, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        // Movimiento: adelante, derecha, adelante, izquierda (y repetir)
        // Esto debe crear un cuadrado y regresar a la posición original en dirección N
        $executor->execute('FRFRFRFR');

        // Debe volver a la posición original (5,5) después de moverse en las 4 direcciones
        $this->assertEquals(5, $rover->getX());
        $this->assertEquals(5, $rover->getY());
        $this->assertEquals('N', $rover->getDirection());
    }

    /**
     * Verifica que el rover retorna el estado correcto después de ejecutar comandos.
     * El estado debe incluir la posición, dirección y flags de obstáculo/límite.
     */
    public function test_return_state_after_execution()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        $result = $executor->execute('F');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('position', $result);
        $this->assertArrayHasKey('x', $result['position']);
        $this->assertArrayHasKey('y', $result['position']);
        $this->assertArrayHasKey('direction', $result);
        $this->assertArrayHasKey('obstacle_found', $result);
        $this->assertArrayHasKey('boundary_hit', $result);

        $this->assertEquals(0, $result['position']['x']);
        $this->assertEquals(1, $result['position']['y']);
        $this->assertEquals('N', $result['direction']);
        $this->assertFalse($result['obstacle_found']);
        $this->assertFalse($result['boundary_hit']);
    }

    /**
     * Verifica el comportamiento cuando el rover encuentra un obstáculo en medio de varios comandos.
     * El rover debe detenerse al encontrar el obstáculo y no ejecutar los comandos restantes.
     */
    public function test_stop_at_obstacle_mid_commands()
    {
        $rover = new Rover(0, 0, 'E');
        $grid = new Grid(10, 10, [[2, 0]]);
        $executor = new CommandExecutor($rover, $grid);

        // Se mueve 1 hacia el este, encuentra obstáculo en (2,0) y debe detenerse sin cambiar la dirección
        $result = $executor->execute('FFF');

        $this->assertEquals(1, $rover->getX());
        $this->assertEquals(0, $rover->getY());
        $this->assertEquals('E', $rover->getDirection());
        $this->assertTrue($rover->hasObstacle());
        $this->assertEquals(true, $result['obstacle_found']);
    }

    /**
     * Verifica que el rover maneja correctamente un bucle completo de rotación.
     * Después de 4 giros a la derecha o izquierda, debe volver a la dirección inicial.
     */
    public function test_full_rotation_cycle()
    {
        $rover = new Rover(0, 0, 'N');
        $grid = new Grid(10, 10);
        $executor = new CommandExecutor($rover, $grid);

        // Giro completo a la derecha
        $executor->execute('RRRR');
        $this->assertEquals('N', $rover->getDirection());

        // Giro completo a la izquierda
        $executor->execute('LLLL');
        $this->assertEquals('N', $rover->getDirection());
    }
}
