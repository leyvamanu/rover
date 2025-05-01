<?php

namespace Tests\Unit\Services\Rover;

use App\Services\Rover\Grid;
use App\Services\Rover\Obstacle;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    /**
     * Verifica que la cuadrícula se inicializa correctamente con las dimensiones dadas y sin obstáculos.
     * Asegura que las propiedades width y height se asignen correctamente y
     * que la lista de obstáculos esté vacía al inicio.
     */
    public function test_initial_grid_without_obstacles()
    {
        $grid = new Grid(10, 10);

        $this->assertEquals(10, $grid->getWidth());
        $this->assertEquals(10, $grid->getHeight());
        $this->assertEmpty($grid->getObstacles());
    }

    /**
     * Verifica que un obstáculo se puede añadir correctamente a la cuadrícula.
     * Usa el método `addObstacle` y comprueba que el obstáculo es identificado en la posición correcta.
     */
    public function test_add_obstacle()
    {
        $grid = new Grid(10, 10);
        $obstacle = new Obstacle(1, 1);

        $grid->addObstacle($obstacle);

        $this->assertTrue($grid->isObstacle(1, 1));
    }

    /**
     * Verifica que se pueden inicializar obstáculos en el grid con un array de coordenadas.
     * Comprueba que el constructor procesa correctamente los obstáculos en formato [[x1, y1], [x2, y2]].
     */
    public function test_initial_grid_with_obstacles_array()
    {
        $obstacles = [[1, 1], [2, 2]];
        $grid = new Grid(10, 10, $obstacles);

        $this->assertTrue($grid->isObstacle(1, 1));
        $this->assertTrue($grid->isObstacle(2, 2));
    }

    /**
     * Verifica que la cuadrícula se inicializa correctamente con una lista de instancias Obstacle.
     * Asegura que los obstáculos proporcionados como objetos sean reconocidos al llamar a `isObstacle`.
     */
    public function test_initial_grid_with_obstacle_objects()
    {
        $obstacles = [new Obstacle(1, 1), new Obstacle(2, 2)];
        $grid = new Grid(10, 10, $obstacles);

        $this->assertTrue($grid->isObstacle(1, 1));
        $this->assertTrue($grid->isObstacle(2, 2));
    }

    /**
     * Verifica que el método `isWithinBounds` comprueba correctamente si una posición está dentro de los límites.
     * Se prueban múltiples escenarios, incluyendo posiciones en los bordes y fuera de los límites.
     */
    public function test_is_within_bounds()
    {
        $grid = new Grid(10, 10);

        $this->assertTrue($grid->isWithinBounds(0, 0));
        $this->assertTrue($grid->isWithinBounds(9, 9));
        $this->assertTrue($grid->isWithinBounds(5, 5));

        $this->assertFalse($grid->isWithinBounds(-1, 0));
        $this->assertFalse($grid->isWithinBounds(0, -1));
        $this->assertFalse($grid->isWithinBounds(10, 0));
        $this->assertFalse($grid->isWithinBounds(0, 10));
    }

    /**
     * Verifica que el método `isObstacle` detecta correctamente un obstáculo en una posición específica
     * y que retorna false cuando no hay un obstáculo en la posición indicada.
     */
    public function test_is_obstacle()
    {
        $grid = new Grid(10, 10);
        $grid->addObstacle(new Obstacle(1, 1));

        $this->assertTrue($grid->isObstacle(1, 1));
        $this->assertFalse($grid->isObstacle(2, 2));
    }

    /**
     * Verifica que se pueden añadir múltiples obstáculos secuencialmente usando `addObstacle`.
     * Comprueba que los obstáculos añadidos uno a uno sean correctamente registrados.
     */
    public function test_add_multiple_obstacles()
    {
        $grid = new Grid(10, 10);

        $grid->addObstacle(new Obstacle(1, 1));
        $grid->addObstacle(new Obstacle(3, 3));
        $grid->addObstacle(new Obstacle(4, 5));

        $this->assertTrue($grid->isObstacle(1, 1));
        $this->assertTrue($grid->isObstacle(3, 3));
        $this->assertTrue($grid->isObstacle(4, 5));
        $this->assertFalse($grid->isObstacle(2, 2));
    }

    /**
     * Verifica que el grid se inicializa correctamente con una mezcla de obstáculos en formato
     * array [[x, y]] y objetos `Obstacle`.
     */
    public function test_grid_initialization_with_mixed_obstacle_inputs()
    {
        $obstacles = [new Obstacle(1, 4), [3, 5]];
        $grid = new Grid(10, 10, $obstacles);

        $this->assertTrue($grid->isObstacle(1, 4));
        $this->assertTrue($grid->isObstacle(3, 5));
        $this->assertFalse($grid->isObstacle(2, 2));
    }

    /**
     * Verifica que los obstáculos fuera del rango de la cuadrícula no son añadidos.
     * Este test asume que los obstáculos fuera de los límites son descartados por el constructor.
     */
    public function test_initialize_with_out_of_bound_obstacles()
    {
        $obstacles = [new Obstacle(15, 15), [20, 20]];
        $grid = new Grid(10, 10, $obstacles);

        $this->assertEmpty($grid->getObstacles());
        $this->assertFalse($grid->isObstacle(15, 15));
        $this->assertFalse($grid->isObstacle(20, 20));
    }

    /**
     * Verifica que no se pueden crear cuadrículas con dimensiones negativas.
     * Este test lanza una excepción de tipo `InvalidArgumentException`.
     */
    public function test_initialize_with_negative_dimensions()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Grid(-10, -10);
    }

    /**
     * Verifica que los obstáculos en las posiciones límite y esquinas de la cuadrícula
     * se manejan correctamente.
     */
    public function test_obstacles_corner_cases()
    {
        $grid = new Grid(10, 10, [[0, 0], [9, 9], [0, 9], [9, 0]]);

        $this->assertTrue($grid->isObstacle(0, 0));
        $this->assertTrue($grid->isObstacle(9, 9));
        $this->assertTrue($grid->isObstacle(0, 9));
        $this->assertTrue($grid->isObstacle(9, 0));
    }
}
