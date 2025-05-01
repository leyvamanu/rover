<?php

namespace Tests\Unit\Services\Rover;

use App\Services\Rover\Rover;
use PHPUnit\Framework\TestCase;

class RoverTest extends TestCase
{
    /**
     * Prueba que el Rover se inicializa correctamente con la posición inicial (x, y) y su dirección.
     */
    public function test_initial_position_and_direction()
    {
        $rover = new Rover(0, 0, 'N');

        // Verifica las coordenadas y dirección inicial
        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(0, $rover->getY());
        $this->assertEquals('N', $rover->getDirection());
    }

    /**
     * Prueba que el Rover lanza una excepción si se inicializa con una dirección no válida.
     */
    public function test_invalid_initial_direction()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Rover(0, 0, 'INVALID');
    }

    /**
     * Verifica que el Rover avanza correctamente hacia el norte.
     */
    public function test_move_forward_north()
    {
        $rover = new Rover(0, 0, 'N');
        $rover->moveForward();

        // Comprueba que el Rover ha avanzado en la coordenada Y
        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(1, $rover->getY());
    }

    /**
     * Verifica que el Rover avanza correctamente hacia el sur.
     */
    public function test_move_forward_south()
    {
        $rover = new Rover(0, 1, 'S');
        $rover->moveForward();

        // Comprueba que el Rover ha retrocedido en la coordenada Y
        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(0, $rover->getY());
    }

    /**
     * Verifica que el Rover avanza correctamente hacia el este.
     */
    public function test_move_forward_east()
    {
        $rover = new Rover(0, 0, 'E');
        $rover->moveForward();

        // Comprueba que el Rover ha avanzado en la coordenada X
        $this->assertEquals(1, $rover->getX());
        $this->assertEquals(0, $rover->getY());
    }

    /**
     * Verifica que el Rover avanza correctamente hacia el oeste.
     */
    public function test_move_forward_west()
    {
        $rover = new Rover(1, 0, 'W');
        $rover->moveForward();

        // Comprueba que el Rover ha retrocedido en la coordenada X
        $this->assertEquals(0, $rover->getX());
        $this->assertEquals(0, $rover->getY());
    }

    /**
     * Verifica que el Rover gira correctamente hacia la izquierda.
     */
    public function test_turn_left()
    {
        $rover = new Rover(0, 0, 'N');

        $rover->turnLeft();
        $this->assertEquals('W', $rover->getDirection());

        $rover->turnLeft();
        $this->assertEquals('S', $rover->getDirection());

        $rover->turnLeft();
        $this->assertEquals('E', $rover->getDirection());

        $rover->turnLeft();
        $this->assertEquals('N', $rover->getDirection());
    }

    /**
     * Verifica que el Rover gira correctamente hacia la derecha.
     */
    public function test_turn_right()
    {
        $rover = new Rover(0, 0, 'N');

        $rover->turnRight();
        $this->assertEquals('E', $rover->getDirection());

        $rover->turnRight();
        $this->assertEquals('S', $rover->getDirection());

        $rover->turnRight();
        $this->assertEquals('W', $rover->getDirection());

        $rover->turnRight();
        $this->assertEquals('N', $rover->getDirection());
    }

    /**
     * Verifica que combinar giros y movimientos funciona correctamente.
     */
    public function test_combined_turn_and_move_forward()
    {
        $rover = new Rover(0, 0, 'N');

        $rover->turnRight(); // Cambia dirección a 'E'
        $rover->moveForward(); // Avanza una posición hacia el este

        $this->assertEquals(1, $rover->getX());
        $this->assertEquals(0, $rover->getY());
        $this->assertEquals('E', $rover->getDirection());
    }

    /**
     * Verifica que el Rover detecta correctamente un obstáculo.
     */
    public function test_obstacle_detection()
    {
        $rover = new Rover(0, 0, 'N');

        // Verifica que inicialmente no se detecta un obstáculo
        $this->assertFalse($rover->hasObstacle());

        // Marca que se detectó un obstáculo
        $rover->markObstacleFound();
        $this->assertTrue($rover->hasObstacle());
    }

    /**
     * Verifica que el Rover detecta correctamente que ha alcanzado un límite.
     */
    public function test_boundary_detection()
    {
        $rover = new Rover(0, 0, 'N');

        // Verifica que inicialmente no se detecta un límite alcanzado
        $this->assertFalse($rover->hasHitBoundary());

        // Marca que se alcanzó un límite
        $rover->markHitBoundary();
        $this->assertTrue($rover->hasHitBoundary());
    }

    /**
     * Verifica que el método getPosition retorna los valores correctos.
     */
    public function test_get_position()
    {
        $rover = new Rover(1, 2, 'E');

        $position = $rover->getPosition();

        // Asegura que la posición sea correcta
        $this->assertEquals([
            'x' => 1,
            'y' => 2,
            'direction' => 'E'
        ], $position);
    }

    /**
     * Verifica que los estados de detección de límites y obstáculos puedan coexistir.
     */
    public function test_mutually_exclusive_state_boundary_and_obstacle()
    {
        $rover = new Rover(0, 0, 'N');

        // Marca detección de un obstáculo y un límite alcanzado
        $rover->markObstacleFound();
        $rover->markHitBoundary();

        $this->assertTrue($rover->hasObstacle());
        $this->assertTrue($rover->hasHitBoundary());
    }
}
