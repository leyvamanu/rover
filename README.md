# Rover

## 🚀 Descripción

Rover es una aplicación que simula el movimiento de un vehículo explorador (rover) en una cuadrícula bidimensional. La aplicación permite controlar el rover mediante comandos simples y gestionar obstáculos en el terreno.

## 🛠️ Tecnologías

- PHP 8.2+
- Laravel 12

## 📋 Características principales

- **Control de Rover**: Mover el rover mediante comandos simples (avanzar, girar)
- **Detección de obstáculos**: El rover detecta obstáculos y se detiene antes de colisionar
- **Límites de terreno**: El rover reconoce los límites del terreno y evita salirse
- **API RESTful**: Interface HTTP para controlar el rover desde cualquier cliente

## 📥 Instalación

### Requisitos previos

- PHP 8.2 o superior
- Composer

### Pasos de instalación

1. Clone el repositorio:

```shell script
git clone https://github.com/leyvamanu/rover.git
cd rover
```


2. Instale las dependencias PHP:

```shell script
composer install
```

3. Copie el archivo de entorno y configure sus variables:

```shell script
cp .env.example .env
php artisan key:generate
```

4. Inicie el servidor de desarrollo:

```shell script
php artisan serve
```

La aplicación estará disponible en `http://127.0.0.1:8000`.

## 🎮 Uso

### API RESTful

Puede controlar el rover a través de la API RESTful con el siguiente endpoint:

#### Ejecutar comandos

```
POST /api/rover/execute
```


Cuerpo de la petición (JSON):

```json
{
  "position": {
    "x": 0,
    "y": 0
  },
  "direction": "N",
  "commands": "FFRFFLFF",
  "obstacles": [
    {
      "x": 2,
      "y": 1
    }
  ]
}
```


Parámetros:
- `position`: Objeto con coordenadas iniciales del rover (`x`, `y`)
- `direction`: Dirección inicial (`N`, `S`, `E`, `W`)
- `commands`: Cadena de comandos a ejecutar:
    - `F`: Avanzar
    - `R`: Girar a la derecha
    - `L`: Girar a la izquierda
- `obstacles`: (Opcional) Array de objetos con coordenadas de obstáculos (`x`, `y`)

Respuesta (JSON):

```json
{
  "position": {
    "x": 3,
    "y": 4
  },
  "direction": "N",
  "obstacle_found": false,
  "boundary_hit": false
}
```


### Interfaz web

1. Acceda a `http://127.0.0.1:8000` en su navegador
2. Configure la posición inicial y dirección del rover
3. Añada obstáculos si lo desea
4. Ingrese los comandos en el campo de texto o use los botones interactivos
5. Pulse "Ejecutar" para ver el resultado del movimiento

## 🧪 Testing

La aplicación cuenta con pruebas unitarias y de integración. Para ejecutarlas:

```shell script
php artisan test
```


Para ejecutar un grupo específico de pruebas:

```shell script
php artisan test --filter=RoverServiceTest
```


## 📖 Comandos disponibles

- `F`: Mueve el rover hacia adelante una unidad en la dirección actual
- `R`: Gira el rover 90 grados a la derecha (sentido horario)
- `L`: Gira el rover 90 grados a la izquierda (sentido antihorario)

## 🧩 Estructura del código

- **Controllers**: `app/Http/Controllers/RoverController.php`
- **Requests**: `app/Http/Requests/RoverCommandRequest.php`
- **Servicios**: `app/Services/Rover/`
    - `RoverService.php`: Coordina la ejecución de comandos
    - `Rover.php`: Modelo del rover con su estado y capacidades
    - `Grid.php`: Representa el terreno y los obstáculos
    - `CommandExecutor.php`: Ejecuta los comandos en el rover
    - `Obstacle.php`: Representa un obstáculo en el terreno
    - `RoverExecutionResult.php`: DTO para el resultado de la ejecución

---

## 📋 Ejemplo de secuencia de comandos

```
FFRFFLF
```

Interpretación:
1. `F`: Avanzar hacia el norte
2. `F`: Avanzar hacia el norte nuevamente
3. `R`: Girar a la derecha (ahora mirando al este)
4. `F`: Avanzar hacia el este
5. `F`: Avanzar hacia el este nuevamente
6. `L`: Girar a la izquierda (ahora mirando al norte)
7. `F`: Avanzar hacia el norte

Si un rover se encuentra con un obstáculo o intenta moverse fuera de los límites, se detendrá y reportará el problema en la respuesta.
