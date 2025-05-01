<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoverControllerTest extends TestCase
{
    use RefreshDatabase;

    // Test de movimiento exitoso sin obstáculos ni colisiones
    public function test_execute_commands_successfully()
    {
        // Se espera que el rover se mueva hacia el norte 2 veces, gire a la derecha (este), y avance 2 pasos
        // Resultado final: posición (2,2), dirección Este
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FFRFF',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 2, 'y' => 2],
                'direction' => 'E',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de detección de obstáculo durante la ejecución de comandos
    public function test_execute_commands_with_obstacle()
    {
        // Hay un obstáculo en (0,1), el primer paso hacia el norte se bloquea
        // Resultado: posición inicial (0,0), no avanza, pero luego gira y avanza hacia el este
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FFRFF',
            'obstacles' => [['x' => 0, 'y' => 1]]
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 2, 'y' => 0],
                'direction' => 'E',
                'obstacle_found' => true,
                'boundary_hit' => false
            ]);
    }

    // Test de colisión contra el borde inferior del grid
    public function test_execute_commands_hitting_boundary()
    {
        // El rover intenta moverse hacia el sur desde (0,0), fuera del límite
        // Resultado: no se mueve, se detecta el límite
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'S',
            'commands' => 'F',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 0],
                'direction' => 'S',
                'obstacle_found' => false,
                'boundary_hit' => true
            ]);
    }

    // Test de giro completo (360 grados)
    public function test_360_degree_turn()
    {
        // Gira a la derecha 4 veces, vuelve a la misma orientación
        // No se mueve de la posición inicial
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'RRRR',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 0],
                'direction' => 'N',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de movimiento en zigzag
    public function test_zigzag_movement()
    {
        // El rover realiza varios giros y movimientos, terminando en (2,2)
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FRFLFRFL',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 2, 'y' => 2],
                'direction' => 'N',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de movimiento largo hasta el borde del grid
    public function test_long_movement()
    {
        // El rover se mueve hacia el este 199 pasos, justo hasta el borde
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'E',
            'commands' => str_repeat('F', 199),
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 199, 'y' => 0],
                'direction' => 'E',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test con múltiples obstáculos a lo largo del recorrido
    public function test_multiple_obstacles()
    {
        // El rover encuentra un obstáculo antes de llegar al destino previsto
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FFRFF',
            'obstacles' => [
                ['x' => 0, 'y' => 1],
                ['x' => 1, 'y' => 2],
                ['x' => 2, 'y' => 2]
            ]
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 2, 'y' => 0],
                'direction' => 'E',
                'obstacle_found' => true,
                'boundary_hit' => false
            ]);
    }

    // Test con una "pared" de obstáculos que bloquean el avance
    public function test_obstacle_wall()
    {
        // El rover no puede avanzar en ninguna de las 4 instrucciones por obstáculos consecutivos
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FFFF',
            'obstacles' => [
                ['x' => 0, 'y' => 1],
                ['x' => 0, 'y' => 2],
                ['x' => 0, 'y' => 3]
            ]
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 0],
                'direction' => 'N',
                'obstacle_found' => true,
                'boundary_hit' => false
            ]);
    }

    // Test de movimiento en la esquina superior derecha del grid
    public function test_corner_movement()
    {
        // El rover intenta avanzar desde la esquina (199,199) hacia el este, pero no puede
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 199, 'y' => 199],
            'direction' => 'E',
            'commands' => 'F',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 199, 'y' => 199],
                'direction' => 'E',
                'obstacle_found' => false,
                'boundary_hit' => true
            ]);
    }

    // Test que verifica el movimiento desde un punto medio del grid.
    public function test_middle_starting_point()
    {
        // El rover parte en (100, 100) mirando al sur, y se le dan 2 comandos "F".
        // Se espera que avance dos posiciones en el eje Y negativo, hasta (100, 98), sin encontrar obstáculos ni salirse de los límites.
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 100, 'y' => 100],
            'direction' => 'S',
            'commands' => 'FF',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 100, 'y' => 98],
                'direction' => 'S',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de colisiones consecutivas contra los límites del grid
    public function test_consecutive_boundary_hits()
    {
        // El rover intenta avanzar repetidamente fuera del límite inferior sin éxito
        // Resultado final: posición inicial (0,0) y límite detectado
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'S',
            'commands' => 'FFFFF',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 0],
                'direction' => 'S',
                'obstacle_found' => false,
                'boundary_hit' => true
            ]);
    }

    // Test de obstáculos fuera de los límites del grid
    public function test_ignores_obstacles_out_of_bounds()
    {
        // Si hay obstáculos definidos fuera de las coordenadas del grid, el sistema debería ignorarlos
        // Resultado: el rover avanza como si no existieran
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'F',
            'obstacles' => [['x' => 0, 'y' => 300]]
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 1],
                'direction' => 'N',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de movimiento sin límites explícitos en el grid
    public function test_unbounded_grid_movement()
    {
        // El rover se mueve 10 pasos hacia el este en un espacio no limitado
        // Resultado final: posición final (10,0)
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'E',
            'commands' => 'FFFFFFFFFF',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 10, 'y' => 0],
                'direction' => 'E',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de un giro sin movimiento ni comandos de desplazamiento
    public function test_turn_without_movement()
    {
        // El rover realiza un solo giro hacia la izquierda desde el norte
        // Resultado final: posición inicial (0,0), orientación Oeste
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'L',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 0],
                'direction' => 'W',
                'obstacle_found' => false,
                'boundary_hit' => false
            ]);
    }

    // Test de secuencia de comandos extremadamente larga
    public function test_large_command_sequence()
    {
        // El rover intenta avanzar 1000 pasos, pero se detiene al alcanzar el extremo del grid (199,199)
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => str_repeat('F', 1000),
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 0, 'y' => 199],
                'direction' => 'N',
                'obstacle_found' => false,
                'boundary_hit' => true
            ]);
    }

    // Test de comandos válidos mezclados con comandos inválidos
    public function test_mixed_invalid_and_valid_commands()
    {
        // El rover recibe un comando de avance seguido de un comando no reconocido
        // Resultado: la petición debería ser rechazada y devolver un error de validación
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FFXF',
            'obstacles' => []
        ]);

        $response->assertStatus(422);
    }

    // Test desde una posición inicial en el borde del grid
    public function test_edge_start_position()
    {
        // El rover parte desde la coordenada más extrema al este (199,0) e intenta avanzar hacia el este
        // Resultado: no avanza debido al límite del grid
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 199, 'y' => 0],
            'direction' => 'E',
            'commands' => 'FF',
            'obstacles' => []
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 199, 'y' => 0],
                'direction' => 'E',
                'obstacle_found' => false,
                'boundary_hit' => true
            ]);
    }

    // Test de detección de obstáculo al inicio
    public function test_rover_starts_on_obstacle()
    {
        // El rover comienza exactamente en un obstáculo
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 1, 'y' => 1],
            'direction' => 'N',
            'commands' => 'F',
            'obstacles' => [['x' => 1, 'y' => 1]]
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'position' => ['x' => 1, 'y' => 1],
                'direction' => 'N',
                'obstacle_found' => true,
                'boundary_hit' => false
            ]);
    }

    // Test de validación: formato de posición inválido
    public function test_invalid_position_format()
    {
        // Falta la clave "x" dentro de position
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['invalid' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'F',
            'obstacles' => []
        ]);

        $response->assertStatus(422);
    }

    // Test de validación: dirección inválida
    public function test_invalid_direction()
    {
        // Dirección "X" no es válida
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'X',
            'commands' => 'F',
            'obstacles' => []
        ]);

        $response->assertStatus(422);
    }

    // Test de validación: comandos inválidos
    public function test_invalid_commands()
    {
        // Comando "X" no reconocido
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'FX',
            'obstacles' => []
        ]);

        $response->assertStatus(422);
    }

    // Test de validación: formato de obstáculos inválido
    public function test_invalid_obstacles_format()
    {
        // Objeto de obstáculo mal formado, falta "x"
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            'commands' => 'F',
            'obstacles' => [['invalid' => 0, 'y' => 0]]
        ]);

        $response->assertStatus(422);
    }

    // Test de validación: falta un campo obligatorio (commands)
    public function test_missing_required_fields()
    {
        // Falta el campo "commands" en la petición
        $response = $this->postJson('/api/rover/execute', [
            'position' => ['x' => 0, 'y' => 0],
            'direction' => 'N',
            // 'commands' missing
            'obstacles' => []
        ]);

        $response->assertStatus(422);
    }
}
